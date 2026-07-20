<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category')
            ->active()
            ->when($request->q, fn($q) => $q->where(function ($qq) use ($request) {
                $qq->where('name', 'like', "%{$request->q}%")
                   ->orWhere('sku', 'like', "%{$request->q}%")
                   ->orWhere('barcode', 'like', "%{$request->q}%");
            }))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 50));

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('category');
        return response()->json(['data' => $product]);
    }
}
