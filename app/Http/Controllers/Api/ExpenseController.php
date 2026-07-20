<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $expenses = Expense::with('user')
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->date_from, fn($q) => $q->whereDate('expense_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('expense_date', '<=', $request->date_to))
            ->latest('expense_date')
            ->paginate($request->integer('per_page', 50));

        return response()->json($expenses);
    }

    public function show(Expense $expense): JsonResponse
    {
        $expense->load('user');
        return response()->json(['data' => $expense]);
    }
}
