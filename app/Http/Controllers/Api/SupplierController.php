<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $suppliers = Supplier::query()
            ->when($request->q, fn($q) => $q->where(function ($qq) use ($request) {
                $qq->where('name', 'like', "%{$request->q}%")
                   ->orWhere('company', 'like', "%{$request->q}%")
                   ->orWhere('phone', 'like', "%{$request->q}%");
            }))
            ->withCount('purchaseOrders')
            ->orderBy('name')
            ->paginate($request->integer('per_page', 50));

        return response()->json($suppliers);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->loadCount('purchaseOrders');
        $supplier->load('purchaseOrders' => fn($q) => $q->latest()->limit(20));

        return response()->json(['data' => $supplier]);
    }
}
