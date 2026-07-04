<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\PosTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    // Middleware applied via routes in Laravel 13

    public function sales(Request $request): View
    {
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->date('end_date', now()->toDateString());
        $groupBy = $request->string('group_by', 'day')->toString(); // day, week, month

        $transactions = PosTransaction::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(50);

        // Summary stats
        $summary = [
            'total_transactions' => PosTransaction::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])->count(),
            'total_revenue' => PosTransaction::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])->sum('total'),
            'avg_transaction' => PosTransaction::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])->avg('total'),
            'total_items_sold' => DB::table('pos_transaction_items')
                ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
                ->whereBetween('pos_transactions.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('pos_transaction_items.qty'),
        ];

        // Chart data
        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $chartData = PosTransaction::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])
        ->select(
            DB::raw("DATE_FORMAT(created_at, '$dateFormat') as period"),
            DB::raw('COUNT(*) as transaction_count'),
            DB::raw('SUM(total) as revenue')
        )
        ->groupBy('period')
        ->orderBy('period')
        ->get();

        return view('pos.reports.sales', compact('transactions', 'summary', 'chartData', 'startDate', 'endDate', 'groupBy'));
    }

    public function products(Request $request): View
    {
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->date('end_date', now()->toDateString());
        $categoryId = $request->integer('category', 0);

        $topProducts = DB::table('pos_transaction_items')
            ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->join('pos_products', 'pos_transaction_items.product_id', '=', 'pos_products.id')
            ->whereBetween('pos_transactions.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->when($categoryId > 0, fn($q) => $q->where('pos_products.category_id', $categoryId))
            ->select(
                'pos_products.id',
                'pos_products.name',
                'pos_products.sku',
                DB::raw('SUM(pos_transaction_items.qty) as total_qty'),
                DB::raw('SUM(pos_transaction_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT pos_transaction_items.transaction_id) as transaction_count')
            )
            ->groupBy('pos_products.id', 'pos_products.name', 'pos_products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit(50)
            ->get();

        $categories = Category::active()->orderBy('name')->get();

        $summary = [
            'total_products_sold' => DB::table('pos_transaction_items')
                ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
                ->whereBetween('pos_transactions.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('pos_transaction_items.qty'),
            'unique_products' => DB::table('pos_transaction_items')
                ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
                ->whereBetween('pos_transactions.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->distinct('pos_transaction_items.product_id')
                ->count('pos_transaction_items.product_id'),
        ];

        return view('pos.reports.products', compact('topProducts', 'categories', 'summary', 'startDate', 'endDate', 'categoryId'));
    }

    public function inventory(Request $request): View
    {
        $stockFilter = $request->string('stock', 'all')->toString();
        $categoryId = $request->integer('category', 0);

        $products = Product::with('category')
            ->when($categoryId > 0, fn($q) => $q->where('category_id', $categoryId))
            ->when($stockFilter === 'low', fn($q) => $q->lowStock())
            ->when($stockFilter === 'out', fn($q) => $q->outOfStock())
            ->orderBy('stock', 'asc')
            ->paginate(50);

        $categories = Category::active()->orderBy('name')->get();

        $summary = [
            'total_products' => Product::count(),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'total_value' => (float) Product::sum(DB::raw('stock * COALESCE(cost, price)')),
        ];

        return view('pos.reports.inventory', compact('products', 'categories', 'summary', 'stockFilter', 'categoryId'));
    }

    public function export(Request $request)
    {
        $type = $request->string('type')->toString();
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->date('end_date', now()->toDateString());

        // Simple CSV export
        $filename = "report_{$type}_" . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            if ($type === 'sales') {
                fputcsv($file, ['Invoice', 'Date', 'Customer', 'Items', 'Subtotal', 'Discount', 'Tax', 'Total', 'Payment Method', 'Cashier']);
                
                PosTransaction::with(['customer', 'user'])
                    ->whereBetween('created_at', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->chunk(100, function($transactions) use ($file) {
                        foreach ($transactions as $t) {
                            fputcsv($file, [
                                $t->invoice_no,
                                $t->created_at->format('Y-m-d H:i:s'),
                                $t->customer?->name ?? '-',
                                $t->items_count,
                                $t->subtotal,
                                $t->discount,
                                $t->tax,
                                $t->total,
                                $t->payment_method,
                                $t->user?->name ?? '-',
                            ]);
                        }
                    });
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
