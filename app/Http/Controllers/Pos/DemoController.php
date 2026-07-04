<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class DemoController extends Controller
{
    /**
     * Show demo cashier page with sample data.
     * This is a public route that does not require authentication.
     * Designed to be embedded in an iframe on the main website.
     */
    public function index(): View
    {
        // Get sample categories and products for demo
        $categories = Category::active()->orderBy('sort_order')->limit(5)->get();
        $products = Product::with('category')->active()->orderBy('name')->limit(12)->get();
        
        // Demo data
        $openShift = (object) [
            'id' => 999,
            'shift_number' => 'DEMO-001',
            'opened_at' => now()->format('d M Y H:i'),
            'opening_balance' => 1000000,
        ];
        
        return view('pos.demo.cashier', compact('openShift', 'categories', 'products'));
    }
}
