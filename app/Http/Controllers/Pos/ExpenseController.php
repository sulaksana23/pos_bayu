<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest('expense_date')->paginate(20);

        $categories = [
            'operational' => 'Operasional',
            'utilities'   => 'Utilitas',
            'rent'        => 'Sewa',
            'salary'      => 'Gaji',
            'maintenance' => 'Perawatan',
            'marketing'   => 'Marketing',
            'other'       => 'Lainnya',
        ];

        $totalAmount = (clone $query)->sum('amount');

        return view('pos.expenses.index', compact('expenses', 'categories', 'totalAmount'));
    }

    public function create()
    {
        $categories = [
            'operational' => 'Operasional',
            'utilities'   => 'Utilitas (Listrik, Air, Internet)',
            'rent'        => 'Sewa',
            'salary'      => 'Gaji Karyawan',
            'maintenance' => 'Perawatan & Perbaikan',
            'marketing'   => 'Marketing & Iklan',
            'other'       => 'Lainnya',
        ];
        return view('pos.expenses.form', ['expense' => new Expense, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category'       => 'required|string|max:50',
            'amount'         => 'required|numeric|min:0',
            'description'    => 'required|string|max:500',
            'expense_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:20',
            'receipt_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes'          => 'nullable|string',
        ]);

        $data['expense_no'] = Expense::generateExpenseNo();
        $data['user_id']    = auth()->id();

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')
                ->store('expenses/receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('pos.expenses.index')
            ->with('success', 'Biaya berhasil dicatat.');
    }

    public function edit(Expense $expense)
    {
        $categories = [
            'operational' => 'Operasional',
            'utilities'   => 'Utilitas (Listrik, Air, Internet)',
            'rent'        => 'Sewa',
            'salary'      => 'Gaji Karyawan',
            'maintenance' => 'Perawatan & Perbaikan',
            'marketing'   => 'Marketing & Iklan',
            'other'       => 'Lainnya',
        ];
        return view('pos.expenses.form', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'category'       => 'required|string|max:50',
            'amount'         => 'required|numeric|min:0',
            'description'    => 'required|string|max:500',
            'expense_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:20',
            'receipt_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes'          => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_image')) {
            // Hapus receipt lama jika ada
            if ($expense->receipt_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->receipt_image);
            }
            $data['receipt_image'] = $request->file('receipt_image')
                ->store('expenses/receipts', 'public');
        } elseif ($request->boolean('delete_receipt') && $expense->receipt_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->receipt_image);
            $data['receipt_image'] = null;
        }

        $expense->update($data);

        return redirect()->route('pos.expenses.index')
            ->with('success', 'Biaya berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->receipt_image);
        }
        $expense->delete();
        return redirect()->route('pos.expenses.index')
            ->with('success', 'Biaya berhasil dihapus.');
    }
}
