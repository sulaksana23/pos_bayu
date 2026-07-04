<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function searchProduct(Request $request): JsonResponse
    {
        $q = $request->string('q')->toString();
        $limit = min(20, max(1, $request->integer('limit', 12)));
        $products = Product::with('category')
            ->active()
            ->where(function ($w) use ($q) {
                if ($q !== '') {
                    $w->where('name','like',"%$q%")
                      ->orWhere('sku','like',"%$q%")
                      ->orWhere('barcode','like',"%$q%");
                }
            })
            ->orderBy('name')->limit($limit)
            ->get(['id','category_id','name','sku','barcode','price','stock','unit','image_url']);

        return response()->json($products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'barcode' => $p->barcode,
            'price' => (float)$p->price,
            'stock' => (int)$p->stock,
            'unit' => $p->unit,
            'category' => optional($p->category)->name,
            'category_color' => optional($p->category)->color,
            'image_url' => $p->image_url ?: 'https://ui-avatars.com/api/?name='.urlencode($p->name).'&size=128&background=random&color=fff',
        ]));
    }

    public function searchCustomer(Request $request): JsonResponse
    {
        $q = $request->string('q')->toString();
        $customers = Customer::active()
            ->when($q !== '', fn ($qq) => $qq->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")->orWhere('phone','like',"%$q%");
            }))
            ->limit(10)
            ->get(['id','name','phone','points']);
        return response()->json($customers);
    }
}
