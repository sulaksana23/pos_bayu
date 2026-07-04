<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PosStockMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryController extends Controller
{
    // Middleware applied via routes in Laravel 13

    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $category = $request->integer('category', 0);
        $stockFilter = $request->string('stock')->toString();

        $products = Product::with('category')
            ->when($q !== '', fn ($qq) => $qq->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")->orWhere('sku','like',"%$q%")->orWhere('barcode','like',"%$q%");
            }))
            ->when($category > 0, fn ($qq) => $qq->where('category_id', $category))
            ->when($stockFilter === 'low', fn ($qq) => $qq->whereColumn('stock','<=','min_stock')->where('stock','>',0))
            ->when($stockFilter === 'out', fn ($qq) => $qq->where('stock','<=',0))
            ->latest()
            ->paginate(20)->withQueryString();

        $categories = Category::active()->orderBy('name')->get();
        $stats = [
            'total' => Product::count(),
            'low' => Product::lowStock()->count(),
            'out' => Product::outOfStock()->count(),
            'value' => (float) Product::sum(DB::raw('stock * COALESCE(cost,0)')),
        ];

        return view('pos.inventory.index', compact('products','categories','stats','q','category','stockFilter'));
    }

    public function create(): View {
        $categories = Category::active()->orderBy('name')->get();
        return view('pos.inventory.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:128'],
            'sku' => ['required','string','max:64','unique:pos_products,sku'],
            'barcode' => ['nullable','string','max:64','unique:pos_products,barcode'],
            'category_id' => ['nullable','integer','exists:pos_categories,id'],
            'price' => ['required','numeric','min:0'],
            'cost' => ['nullable','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'min_stock' => ['required','integer','min:0'],
            'unit' => ['required','string','max:16'],
            'description' => ['nullable','string'],
            'image_url' => ['nullable','url'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $product = Product::create($data);

        if ((int)$data['stock'] > 0) {
            PosStockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => 'in',
                'qty' => (int)$data['stock'],
                'unit_cost' => $data['cost'] ?? null,
                'reference_type' => 'manual',
                'notes' => 'Stok awal saat tambah produk',
            ]);
        }
        return redirect()->route('pos.inventory.index')->with('success','Produk '.$product->name.' ditambahkan.');
    }

    public function edit(Product $inventory): View {
        $categories = Category::active()->orderBy('name')->get();
        $product = $inventory;
        return view('pos.inventory.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $inventory): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:128'],
            'sku' => ['required','string','max:64', Rule::unique('pos_products','sku')->ignore($inventory->id)],
            'barcode' => ['nullable','string','max:64', Rule::unique('pos_products','barcode')->ignore($inventory->id)],
            'category_id' => ['nullable','integer','exists:pos_categories,id'],
            'price' => ['required','numeric','min:0'],
            'cost' => ['nullable','numeric','min:0'],
            'min_stock' => ['required','integer','min:0'],
            'unit' => ['required','string','max:16'],
            'description' => ['nullable','string'],
            'image_url' => ['nullable','url'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $inventory->update($data);
        return redirect()->route('pos.inventory.index')->with('success','Produk diperbarui.');
    }

    public function destroy(Request $request, Product $inventory): RedirectResponse
    {
        $inventory->update(['is_active' => false]);
        return back()->with('success','Produk dinonaktifkan.');
    }

    public function show(Product $inventory): View {
        $inventory->load(['category','stockMovements.user','transactionItems.transaction']);
        $movements = $inventory->stockMovements()->latest('created_at')->limit(50)->get();
        return view('pos.inventory.show', compact('inventory','movements'));
    }

    public function adjust(Request $request, Product $inventory): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['in','out','adjust'])],
            'qty' => ['required','integer','min:1'],
            'unit_cost' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string','max:500'],
        ]);

        try {
            DB::transaction(function () use ($inventory, $data, $request) {
                // Lock the product row to serialize concurrent adjustments.
                $locked = \App\Models\Product::lockForUpdate()->find($inventory->id);
                $signedQty = $data['type'] === 'in' ? (int)$data['qty']
                    : ($data['type'] === 'out' ? -1 * (int)$data['qty'] : (int)$data['qty']);
                if ($data['type'] === 'adjust') {
                    $signedQty = (int)$data['qty'] - (int)$locked->stock;
                }
                // Reject if type=out would drive stock negative (or any 'out'/'adjust' change that would).
                if ($signedQty !== 0 && (int)$locked->stock + $signedQty < 0) {
                    throw new \RuntimeException(
                        'Stok tidak cukup untuk "'.$locked->name.'": tersisa '.$locked->stock
                        .' di laci/dgudang, permintaan '.abs($signedQty).' '.$locked->unit
                    );
                }
                if ($signedQty !== 0) {
                    $locked->increment('stock', $signedQty);
                }

                PosStockMovement::create([
                    'product_id' => $inventory->id,
                    'user_id' => $request->user()->id,
                    'type' => $data['type'],
                    'qty' => $signedQty,
                    'unit_cost' => $data['unit_cost'] ?? null,
                    'reference_type' => 'manual',
                    'notes' => $data['notes'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyesuaikan stok: '.$e->getMessage());
        }

        return back()->with('success','Stok berhasil disesuaikan.');
    }
}
