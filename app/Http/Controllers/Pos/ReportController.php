<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\PosTransaction;
use App\Models\Customer;
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
            ->leftJoin('pos_categories', 'pos_products.category_id', '=', 'pos_categories.id')
            ->whereBetween('pos_transactions.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->when($categoryId > 0, fn($q) => $q->where('pos_products.category_id', $categoryId))
            ->select(
                'pos_products.id',
                'pos_products.name as product_name',
                'pos_products.sku as product_sku',
                'pos_categories.name as category_name',
                DB::raw('SUM(pos_transaction_items.qty) as total_qty'),
                DB::raw('SUM(pos_transaction_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT pos_transaction_items.transaction_id) as transaction_count')
            )
            ->groupBy('pos_products.id', 'pos_products.name', 'pos_products.sku', 'pos_categories.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(50)
            ->get();

        $categories = Category::active()->orderBy('name')->get();

        $totalRevenue = DB::table('pos_transaction_items')
            ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->whereBetween('pos_transactions.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->sum('pos_transaction_items.subtotal');

        $totalQty = DB::table('pos_transaction_items')
            ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->whereBetween('pos_transactions.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->sum('pos_transaction_items.qty');

        $uniqueProducts = DB::table('pos_transaction_items')
            ->join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->whereBetween('pos_transactions.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->distinct('pos_transaction_items.product_id')
            ->count('pos_transaction_items.product_id');

        $summary = [
            'total_items'    => $totalQty,
            'total_revenue'  => (float) $totalRevenue,
            'unique_products' => $uniqueProducts,
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

    public function expenses(Request $request): View
    {
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->date('end_date', now()->toDateString());
        $category  = $request->string('category', '');

        $query = Expense::with('user')
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->when($category, fn($q) => $q->where('category', $category));

        $expenses = (clone $query)->latest('expense_date')->paginate(50);

        $categories = [
            'operational' => 'Operasional',
            'utilities'   => 'Utilitas',
            'rent'        => 'Sewa',
            'salary'      => 'Gaji',
            'maintenance' => 'Perawatan',
            'marketing'   => 'Marketing',
            'other'       => 'Lainnya',
        ];

        // Summary
        $summary = [
            'total_expenses' => (clone $query)->sum('amount'),
            'total_count'    => (clone $query)->count(),
            'avg_expense'    => (clone $query)->avg('amount'),
        ];

        // Breakdown by category
        $breakdown = (clone $query)
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('pos.reports.expenses', compact(
            'expenses', 'summary', 'breakdown', 'categories',
            'startDate', 'endDate', 'category'
        ));
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
                
                PosTransaction::with(['customer', 'user'])->withCount('items')
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

    public function customers(Request $request): View
    {
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->date('end_date', now()->toDateString());

        // Top pelanggan berdasarkan total belanja pada periode
        $topCustomers = DB::table('pos_customers')
            ->leftJoin('pos_transactions', function ($join) use ($startDate, $endDate) {
                $join->on('pos_transactions.customer_id', '=', 'pos_customers.id')
                     ->whereBetween('pos_transactions.created_at', [
                         $startDate . ' 00:00:00',
                         $endDate   . ' 23:59:59',
                     ]);
            })
            ->select(
                'pos_customers.id',
                'pos_customers.name',
                'pos_customers.phone',
                DB::raw('COUNT(pos_transactions.id) as transactions_count'),
                DB::raw('COALESCE(SUM(pos_transactions.total), 0) as total_spend'),
                DB::raw('MAX(pos_transactions.created_at) as last_transaction')
            )
            ->groupBy('pos_customers.id', 'pos_customers.name', 'pos_customers.phone')
            ->orderByDesc('total_spend')
            ->limit(50)
            ->get();

        $summary = [
            'total_customers'  => DB::table('pos_customers')->count(),
            'new_customers'    => DB::table('pos_customers')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
            'active_customers' => DB::table('pos_transactions')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('customer_id')
                ->distinct('customer_id')
                ->count('customer_id'),
            'avg_spend'        => DB::table('pos_transactions')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('customer_id')
                ->avg('total') ?? 0,
        ];

        return view('pos.reports.customers', compact('topCustomers', 'summary', 'startDate', 'endDate'));
    }
}
