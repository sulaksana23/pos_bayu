<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    // Middleware applied via routes in Laravel 13

    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        
        $categories = Category::query()
            ->withCount('products')
            ->when($q !== '', fn ($qq) => $qq->where('name','like',"%$q%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('pos.categories.index', compact('categories', 'q'));
    }

    public function create(): View
    {
        return view('pos.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:64','unique:pos_categories,name'],
            'description' => ['nullable','string','max:255'],
            'sort_order' => ['required','integer','min:0'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Category::create($data);

        return redirect()->route('pos.categories.index')->with('success','Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('pos.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:64', Rule::unique('pos_categories','name')->ignore($category->id)],
            'description' => ['nullable','string','max:255'],
            'sort_order' => ['required','integer','min:0'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $category->update($data);

        return redirect()->route('pos.categories.index')->with('success','Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error','Kategori tidak dapat dihapus karena masih memiliki produk.');
        }
        
        $category->delete();
        return redirect()->route('pos.categories.index')->with('success','Kategori berhasil dihapus.');
    }
}
