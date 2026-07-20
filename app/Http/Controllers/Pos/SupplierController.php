<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(15);
        return view('pos.suppliers.index', compact('suppliers'));
    }

    public function show(Supplier $supplier)
    {
        $supplier->loadCount('purchaseOrders');
        $supplier->load(['purchaseOrders' => fn($q) => $q->with('supplier', 'user')->latest()->limit(20)]);
        return view('pos.suppliers.show', compact('supplier'));
    }

    public function create()
    {
        return view('pos.suppliers.form', ['supplier' => new Supplier]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'company'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:255',
            'address'   => 'nullable|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:30',
            'tax_id'    => 'nullable|string|max:50',
            'notes'     => 'nullable|string',
        ]);

        Supplier::create($data);

        return redirect()->route('pos.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('pos.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'company'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:255',
            'address'   => 'nullable|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:30',
            'tax_id'    => 'nullable|string|max:50',
            'notes'     => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', $supplier->is_active);
        $supplier->update($data);

        return redirect()->route('pos.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->exists()) {
            return back()->with('error', 'Supplier tidak bisa dihapus karena memiliki purchase order.');
        }
        $supplier->delete();
        return redirect()->route('pos.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function toggle(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        return back()->with('success', 'Status supplier diperbarui.');
    }
}
