<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $transactions = PosTransaction::with(['customer', 'cashier'])
            ->completed()
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate($request->integer('per_page', 50));

        return response()->json($transactions);
    }

    public function show(PosTransaction $transaction): JsonResponse
    {
        $transaction->load(['customer', 'cashier', 'items', 'stockMovements']);
        return response()->json(['data' => $transaction]);
    }
}
