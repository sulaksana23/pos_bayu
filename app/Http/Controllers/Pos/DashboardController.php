<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosShift;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\PosStockMovement;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today          = Carbon::today();
        $yesterday      = Carbon::yesterday();
        $monthStart     = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd   = Carbon::now()->subMonth()->endOfMonth();
        $weekStart      = Carbon::now()->subDays(6)->startOfDay();

        // ── Today stats ──────────────────────────────────────────────────────
        $todaySales   = PosTransaction::completed()->whereDate('created_at', $today)->sum('total');
        $todayCount   = PosTransaction::completed()->whereDate('created_at', $today)->count();
        $todayCash    = PosTransaction::completed()->where('payment_method', 'cash')->whereDate('created_at', $today)->sum('paid');
        $todayNonCash = PosTransaction::completed()->whereIn('payment_method', ['qris', 'transfer', 'wallet'])->whereDate('created_at', $today)->sum('paid');

        // ── Yesterday comparison ─────────────────────────────────────────────
        $yesterdaySales = PosTransaction::completed()->whereDate('created_at', $yesterday)->sum('total');
        $yesterdayCount = PosTransaction::completed()->whereDate('created_at', $yesterday)->count();

        // ── Month stats ───────────────────────────────────────────────────────
        $monthSales     = PosTransaction::completed()->whereBetween('created_at', [$monthStart, now()])->sum('total');
        $monthCount     = PosTransaction::completed()->whereBetween('created_at', [$monthStart, now()])->count();
        $lastMonthSales = PosTransaction::completed()->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total');

        // ── Average transaction ───────────────────────────────────────────────
        $avgTransaction = $todayCount > 0 ? $todaySales / $todayCount : 0;

        // ── Weekly trend (7 days) ────────────────────────────────────────────
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $weeklyTrend[] = [
                'date'  => $d->format('d M'),
                'day'   => $d->isoFormat('ddd'),
                'sales' => (float) PosTransaction::completed()->whereDate('created_at', $d)->sum('total'),
                'count' => PosTransaction::completed()->whereDate('created_at', $d)->count(),
            ];
        }

        // ── Hourly sales today (06:00 – current hour) ────────────────────────
        $currentHour = (int) Carbon::now()->format('H');
        $hourlyTrend = [];
        for ($h = 6; $h <= min($currentHour, 23); $h++) {
            $from = Carbon::today()->setHour($h)->startOfHour();
            $to   = Carbon::today()->setHour($h)->endOfHour();
            $hourlyTrend[] = [
                'hour'  => sprintf('%02d:00', $h),
                'sales' => (float) PosTransaction::completed()
                    ->whereBetween('created_at', [$from, $to])
                    ->sum('total'),
                'count' => PosTransaction::completed()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ];
        }

        // ── Top products (7 days) ─────────────────────────────────────────────
        $topProducts = DB::table('pos_transaction_items as ti')
            ->join('pos_transactions as t', 't.id', '=', 'ti.transaction_id')
            ->where('t.status', 'completed')
            ->whereDate('t.created_at', '>=', $weekStart)
            ->select(
                'ti.product_name',
                DB::raw('SUM(ti.qty) as qty'),
                DB::raw('SUM(ti.subtotal) as revenue')
            )
            ->groupBy('ti.product_name')
            ->orderByDesc('revenue')
            ->limit(8)->get();

        // ── Recent transactions ───────────────────────────────────────────────
        $recent = PosTransaction::with(['cashier', 'customer', 'items'])
            ->completed()->latest('created_at')->limit(10)->get();

        // ── Stock info ────────────────────────────────────────────────────────
        $lowStockCount  = Product::lowStock()->count();
        $outOfStock     = Product::outOfStock()->count();
        $totalProducts  = Product::active()->count();

        // ── Low stock products (top 5) ────────────────────────────────────────
        $lowStockProducts = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get(['id', 'name', 'stock', 'min_stock', 'unit']);

        $outOfStockProducts = Product::where('is_active', true)
            ->where('stock', '<=', 0)
            ->orderBy('name')
            ->limit(5)
            ->get(['id', 'name', 'stock', 'unit']);

        // ── Customer stats ────────────────────────────────────────────────────
        $totalCustomers    = Customer::count();
        $newCustomersToday = Customer::whereDate('created_at', $today)->count();
        $newCustomersMonth = Customer::whereDate('created_at', '>=', $monthStart)->count();

        // ── Payment method breakdown today ────────────────────────────────────
        $paymentBreakdown = PosTransaction::completed()
            ->whereDate('created_at', $today)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        // ── Active shifts & cashiers ──────────────────────────────────────────
        $openShift      = auth()->user()->currentShift();
        $activeShifts   = PosShift::with('user')->where('status', 'open')->get();
        $activeCashiers = $activeShifts->count();

        // ── Shift summary for current user ────────────────────────────────────
        $shiftSales = 0;
        $shiftCount = 0;
        if ($openShift) {
            $shiftSales = PosTransaction::completed()
                ->where('shift_id', $openShift->id)
                ->sum('total');
            $shiftCount = PosTransaction::completed()
                ->where('shift_id', $openShift->id)
                ->count();
        }

        // ── Growth percentages ────────────────────────────────────────────────
        $salesGrowth = $yesterdaySales > 0
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : ($todaySales > 0 ? 100 : 0);

        $monthGrowth = $lastMonthSales > 0
            ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($monthSales > 0 ? 100 : 0);

        $countGrowth = $yesterdayCount > 0
            ? round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100, 1)
            : ($todayCount > 0 ? 100 : 0);

        return view('pos.dashboard', compact(
            'todaySales', 'todayCount', 'todayCash', 'todayNonCash',
            'yesterdaySales', 'yesterdayCount',
            'monthSales', 'monthCount', 'lastMonthSales',
            'avgTransaction', 'salesGrowth', 'monthGrowth', 'countGrowth',
            'weeklyTrend', 'hourlyTrend', 'topProducts', 'recent',
            'lowStockCount', 'outOfStock', 'totalProducts',
            'lowStockProducts', 'outOfStockProducts',
            'totalCustomers', 'newCustomersToday', 'newCustomersMonth',
            'paymentBreakdown',
            'openShift', 'activeShifts', 'activeCashiers',
            'shiftSales', 'shiftCount'
        ));
    }
}
