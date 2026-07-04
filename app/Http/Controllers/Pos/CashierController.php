<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PosShift;
use App\Models\PosStockMovement;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CashierController extends Controller
{
    // Middleware applied via routes instead of constructor for Laravel 13
    // See routes/web.php for middleware configuration

    public function index(Request $request): View
    {
        $openShift = $request->user()?->currentShift();
        $categories = \App\Models\Category::active()->orderBy('sort_order')->get();
        $products = Product::with('category')->active()->orderBy('name')->limit(48)->get();
        return view('pos.cashier', compact('openShift','categories','products'));
    }

    public function checkout(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:pos_products,id'],
            'items.*.qty' => ['required','numeric','min:0.01'],
            'items.*.price' => ['required','numeric','min:0'],
            'items.*.discount' => ['nullable','numeric','min:0'],
            'customer_id' => ['nullable','integer','exists:pos_customers,id'],
            'discount' => ['nullable','numeric','min:0'],
            'tax' => ['nullable','numeric','min:0'],
            'paid' => ['required','numeric','min:0'],
            'payment_method' => ['required', Rule::in(['cash','qris','transfer','wallet','mixed'])],
            'notes' => ['nullable','string','max:500'],
        ]);

        $shift = $request->user()->currentShift();
        if (! $shift) {
            return back()->with('error','Shift belum dibuka.');
        }

        // Validate stock
        foreach ($data['items'] as $line) {
            $product = Product::find($line['product_id']);
            if ($product->stock < $line['qty']) {
                return back()->with('error', 'Stok '.($product->name).' tidak cukup. Tersisa: '.$product->stock);
            }
        }

        $subtotal = 0;
        foreach ($data['items'] as $line) {
            $lineSubtotal = (float)$line['price'] * (float)$line['qty'] - (float)($line['discount'] ?? 0);
            if ($lineSubtotal < 0) $lineSubtotal = 0;
            $subtotal += $lineSubtotal;
        }
        $totalDiscount = (float)($data['discount'] ?? 0);
        $tax = (float)($data['tax'] ?? 0);
        $total = max(0, $subtotal - $totalDiscount + $tax);
        $paid = (float)$data['paid'];
        $change = max(0, $paid - $total);
        if ($paid < $total) {
            return back()->with('error', 'Jumlah bayar kurang dari total.');
        }

        try {
        $trx = DB::transaction(function () use ($data, $subtotal, $totalDiscount, $tax, $total, $paid, $change, $shift) {
            // Lock all product rows to prevent race conditions on stock decrement.
            $productIds = collect($data['items'])->pluck('product_id')->unique()->all();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            // Re-validate stock INSIDE the transaction (with row locks held).
            foreach ($data['items'] as $line) {
                $p = $products[$line['product_id']] ?? null;
                if (! $p) {
                    throw new \RuntimeException('Produk '.$line['product_id'].' tidak ditemukan.');
                }
                if ((float)$p->stock < (float)$line['qty']) {
                    throw new \RuntimeException('Stok '.$p->name.' tidak cukup. Tersisa: '.$p->stock);
                }
                // Enforce server-side price (clients cannot override)
                $line['price'] = (float)$p->price;
            }

            $trx = PosTransaction::create([
                'invoice_no'       => PosTransaction::generateInvoiceNo(),
                'shift_id'         => $shift->id,
                'user_id'          => $shift->user_id,
                'customer_id'      => $data['customer_id'] ?? null,
                'subtotal'         => $subtotal,
                'discount'         => $totalDiscount,
                'tax'              => $tax,
                'total'            => $total,
                'paid'             => $paid,
                'change_amount'    => $change,
                'payment_method'   => $data['payment_method'],
                'status'           => 'completed',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $line) {
                $product = $products[$line['product_id']];
                $lineSubtotal = max(0, (float)$line['price'] * (float)$line['qty'] - (float)($line['discount'] ?? 0));
                PosTransactionItem::create([
                    'transaction_id' => $trx->id,
                    'product_id'     => $product->id,
                    'product_name'   => $product->name,
                    'product_sku'    => $product->sku,
                    'price'          => $product->price,
                    'cost'           => $product->cost,
                    'qty'            => (int)$line['qty'],
                    'discount'       => $line['discount'] ?? 0,
                    'subtotal'       => $lineSubtotal,
                ]);
                // Now we hold the row lock; safe to decrement.
                $product->decrement('stock', (int)$line['qty']);
                PosStockMovement::create([
                    'product_id'     => $product->id,
                    'user_id'        => auth()->id(),
                    'type'           => 'sale',
                    'qty'            => -1 * (int)$line['qty'],
                    'reference_type' => 'transaction',
                    'reference_id'   => $trx->id,
                ]);
            }

            if (! empty($data['customer_id'])) {
                Customer::where('id', $data['customer_id'])->increment('visit_count');
                Customer::where('id', $data['customer_id'])->increment('total_spent', $total);
            }

            return $trx;
        });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Transaksi gagal: '.$e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'invoice_no' => $trx->invoice_no,
                'redirect_url' => route('pos.cashier.receipt', $trx->id),
            ]);
        }
        return redirect()->route('pos.cashier.receipt', $trx->id)->with('success', 'Transaksi '.$trx->invoice_no.' berhasil.');
    }

    public function receipt(Request $request, PosTransaction $transaction): View {
        $transaction->load('cashier','customer','items');
        return view('pos.cashier.receipt', compact('transaction'));
    }

    public function void(Request $request, PosTransaction $transaction): RedirectResponse
    {
        abort_if($transaction->status !== 'completed', 400, 'Hanya transaksi selesai yang bisa di-void.');
        $data = $request->validate([
            'reason' => ['required','string','max:200'],
        ]);
        $transaction->update([
            'status' => 'void',
            'void_reason' => $data['reason'],
            'voided_at' => now(),
            'voided_by' => $request->user()->id,
        ]);
        // restock
        foreach ($transaction->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)->increment('stock', $item->qty);
                PosStockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id' => $request->user()->id,
                    'type' => 'return',
                    'qty' => (int)$item->qty,
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'notes' => 'Void: '.$data['reason'],
                ]);
            }
        }
        return back()->with('success','Transaksi '.$transaction->invoice_no.' di-void.');
    }
}
