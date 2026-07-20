<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier', 'user', 'items')
            ->latest()
            ->paginate(15);
        return view('pos.purchase_orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products  = Product::active()->orderBy('name')->get(['id', 'name', 'sku', 'price', 'unit']);
        return view('pos.purchase_orders.form', [
            'order' => new PurchaseOrder,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'    => 'required|exists:pos_suppliers,id',
            'order_date'     => 'nullable|date',
            'expected_date'  => 'nullable|date|after_or_equal:order_date',
            'discount'       => 'nullable|numeric|min:0',
            'tax'            => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id'   => 'nullable|exists:pos_products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_sku'  => 'nullable|string|max:50',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.price'        => 'required|numeric|min:0',
        ]);

        $items = $data['items'];
        unset($data['items']);

        $data['po_no']    = PurchaseOrder::generatePoNo();
        $data['user_id']  = auth()->id();
        $data['status']   = 'pending';
        $data['subtotal'] = collect($items)->sum(fn($i) => $i['qty'] * $i['price']);
        $data['discount'] = $data['discount'] ?? 0;
        $data['tax']      = $data['tax'] ?? 0;
        $data['total']    = $data['subtotal'] - $data['discount'] + $data['tax'];

        $order = PurchaseOrder::create($data);

        foreach ($items as $item) {
            $order->items()->create([
                'product_id'   => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'product_sku'  => $item['product_sku'] ?? '',
                'qty'          => $item['qty'],
                'price'        => $item['price'],
                'subtotal'     => $item['qty'] * $item['price'],
            ]);
        }

        return redirect()->route('pos.purchase-orders.show', $order)
            ->with('success', 'Purchase order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'user', 'items.product');
        return view('pos.purchase_orders.show', ['order' => $purchaseOrder]);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['pending'])) {
            return back()->with('error', 'Hanya PO dengan status pending yang bisa diedit.');
        }

        $suppliers = Supplier::active()->orderBy('name')->get();
        $products  = Product::active()->orderBy('name')->get(['id', 'name', 'sku', 'price', 'unit']);
        $purchaseOrder->load('items');

        return view('pos.purchase_orders.form', compact('purchaseOrder', 'suppliers', 'products'))
            ->with('order', $purchaseOrder);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['pending'])) {
            return back()->with('error', 'Hanya PO dengan status pending yang bisa diedit.');
        }

        $data = $request->validate([
            'supplier_id'    => 'required|exists:pos_suppliers,id',
            'order_date'     => 'nullable|date',
            'expected_date'  => 'nullable|date|after_or_equal:order_date',
            'discount'       => 'nullable|numeric|min:0',
            'tax'            => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id'   => 'nullable|exists:pos_products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_sku'  => 'nullable|string|max:50',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.price'        => 'required|numeric|min:0',
        ]);

        $items = $data['items'];
        unset($data['items']);

        $data['subtotal'] = collect($items)->sum(fn($i) => $i['qty'] * $i['price']);
        $data['discount'] = $data['discount'] ?? 0;
        $data['tax']      = $data['tax'] ?? 0;
        $data['total']    = $data['subtotal'] - $data['discount'] + $data['tax'];

        $purchaseOrder->update($data);
        $purchaseOrder->items()->delete();

        foreach ($items as $item) {
            $purchaseOrder->items()->create([
                'product_id'   => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'product_sku'  => $item['product_sku'] ?? '',
                'qty'          => $item['qty'],
                'price'        => $item['price'],
                'subtotal'     => $item['qty'] * $item['price'],
            ]);
        }

        return redirect()->route('pos.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order berhasil diperbarui.');
    }

    public function markOrdered(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'PO sudah diproses.');
        }

        $purchaseOrder->update([
            'status'     => 'ordered',
            'order_date' => $purchaseOrder->order_date ?? now(),
        ]);

        return back()->with('success', 'PO ditandai sebagai sudah dipesan.');
    }

    public function markReceived(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'ordered') {
            return back()->with('error', 'PO harus berstatus "ordered" sebelum diterima.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update([
                'status'        => 'received',
                'received_date' => now(),
            ]);

            // Update stock for each item linked to a product
            foreach ($purchaseOrder->items as $item) {
                if ($item->product_id) {
                    $product = Product::findOrFail($item->product_id);
                    $product->increment('stock', $item->qty);

                    \App\Models\PosStockMovement::create([
                        'product_id'     => $item->product_id,
                        'type'           => 'in',
                        'qty'            => $item->qty,
                        'stock_before'   => $product->stock - $item->qty,
                        'stock_after'    => $product->stock,
                        'reference_type' => 'purchase_order',
                        'reference_id'   => $purchaseOrder->id,
                        'notes'          => 'Penerimaan PO: ' . $purchaseOrder->po_no,
                        'user_id'        => auth()->id(),
                    ]);
                }
            }
        });

        return back()->with('success', 'PO diterima. Stok produk telah diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'PO yang sudah diterima tidak bisa dihapus.');
        }

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('pos.purchase-orders.index')
            ->with('success', 'PO berhasil dihapus.');
    }
}
