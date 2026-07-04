<?php
namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    // Middleware applied via routes in Laravel 13

    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        
        $customers = Customer::query()
            ->withCount('transactions')
            ->when($q !== '', fn ($qq) => $qq->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('phone','like',"%$q%")
                  ->orWhere('email','like',"%$q%");
            }))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('pos.customers.index', compact('customers', 'q'));
    }

    public function create(): View
    {
        return view('pos.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:128'],
            'phone' => ['nullable','string','max:20','unique:pos_customers,phone'],
            'email' => ['nullable','email','max:128','unique:pos_customers,email'],
            'address' => ['nullable','string','max:500'],
            'notes' => ['nullable','string','max:500'],
        ]);

        Customer::create($data);

        return redirect()->route('pos.customers.index')->with('success','Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['transactions' => function($q) {
            $q->latest()->limit(20);
        }]);
        
        $stats = [
            'total_transactions' => $customer->transactions()->count(),
            'total_spent' => $customer->transactions()->sum('total'),
            'avg_transaction' => $customer->transactions()->avg('total'),
        ];

        return view('pos.customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer): View
    {
        return view('pos.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:128'],
            'phone' => ['nullable','string','max:20', Rule::unique('pos_customers','phone')->ignore($customer->id)],
            'email' => ['nullable','email','max:128', Rule::unique('pos_customers','email')->ignore($customer->id)],
            'address' => ['nullable','string','max:500'],
            'notes' => ['nullable','string','max:500'],
        ]);

        $customer->update($data);

        return redirect()->route('pos.customers.index')->with('success','Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->transactions()->exists()) {
            return back()->with('error','Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi.');
        }
        
        $customer->delete();
        return redirect()->route('pos.customers.index')->with('success','Pelanggan berhasil dihapus.');
    }
}
