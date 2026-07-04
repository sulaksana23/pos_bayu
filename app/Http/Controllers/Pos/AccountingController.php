<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosShift;
use App\Models\PosTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountingController extends Controller
{
    public function index(Request $request): View
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $mtd = [
            'sales'    => PosTransaction::where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])->sum('total'),
            'count'    => PosTransaction::where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])->count(),
            'cash'     => PosTransaction::where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])->where('payment_method','cash')->sum('paid'),
            'noncash'  => PosTransaction::where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])->whereIn('payment_method',['qris','transfer','wallet'])->sum('paid'),
            'tax'      => PosTransaction::where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])->sum('tax'),
            'discount' => PosTransaction::where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])->sum('discount'),
        ];
        $mtd['avg'] = $mtd['count'] > 0 ? $mtd['sales'] / $mtd['count'] : 0;

        $dailyTrend = [];
        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $dailyTrend[] = [
                'date' => $cursor->format('d M'),
                'sales' => (float) PosTransaction::where('status','completed')->whereDate('created_at',$cursor)->sum('total'),
                'count' => PosTransaction::where('status','completed')->whereDate('created_at',$cursor)->count(),
            ];
            $cursor->addDay();
        }

        $byMethod = DB::table('pos_transactions')
            ->where('status','completed')->whereBetween('created_at',[$monthStart,$monthEnd])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')->get();

        $recentShifts = PosShift::with('user')->where('status','closed')->latest('closed_at')->limit(15)->get();

        return view('pos.accounting.index', compact('mtd','dailyTrend','byMethod','recentShifts'));
    }

    public function daily(Request $request): View {
        $date = $request->date('date', Carbon::today());
        $transactions = PosTransaction::with(['cashier','customer','items'])
            ->whereDate('created_at',$date)
            ->where('status','completed')->latest()->get();
        return view('pos.accounting.daily', compact('date','transactions'));
    }
}
