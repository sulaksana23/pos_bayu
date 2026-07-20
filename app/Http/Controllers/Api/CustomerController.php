<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->when($request->q, fn($q) => $q->where(function ($qq) use ($request) {
                $qq->where('name', 'like', "%{$request->q}%")
                   ->orWhere('phone', 'like', "%{$request->q}%");
            }))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 50));

        return response()->json($customers);
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->loadCount('transactions');
        $customer->load('transactions' => fn($q) => $q->latest()->limit(20));

        return response()->json(['data' => $customer]);
    }
}
