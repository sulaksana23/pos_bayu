<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\PosTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request): JsonResponse
    {
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->date('end_date', now()->toDateString());

        $summary = [
            'total_transactions' => PosTransaction::completed()
                ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->count(),
            'total_revenue' => (float) PosTransaction::completed()
                ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->sum('total'),
            'avg_transaction' => (float) PosTransaction::completed()
                ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->avg('total') ?? 0,
        ];

        $chartData = PosTransaction::completed()
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as period"),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json([
            'summary'   => $summary,
            'chart'     => $chartData,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function expenses(Request $request): JsonResponse
    {
        $startDate = $request->date('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->date('end_date', now()->toDateString());
        $category  = $request->string('category', '');

        $query = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->when($category, fn($q) => $q->where('category', $category));

        $summary = [
            'total_expenses' => (float) (clone $query)->sum('amount'),
            'total_count'    => (clone $query)->count(),
            'avg_expense'    => (float) (clone $query)->avg('amount') ?? 0,
        ];

        $breakdown = (clone $query)
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'summary'    => $summary,
            'breakdown'  => $breakdown,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }
}
