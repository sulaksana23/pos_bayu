@php
    $isEditing = $expense->exists;
@endphp
@extends('layouts.app')
@section('title', $isEditing ? 'Edit Biaya' : 'Catat Biaya')
@section('breadcrumb', $isEditing ? 'Edit Biaya' : 'Catat Biaya')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form action="{{ $isEditing ? route('pos.expenses.update', $expense) : route('pos.expenses.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @if($isEditing) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <input type="text" name="description"
                           value="{{ old('description', $expense->description) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                           placeholder="Misal: Listrik bulan Juli, Gaji kasir, dll"
                           required>
                    @error('description') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="amount"
                           value="{{ old('amount', $expense->amount) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                           required>
                    @error('amount') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="expense_date"
                           value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                           required>
                    @error('expense_date') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                        <option value="">Pilih...</option>
                        <option value="cash" {{ old('payment_method', $expense->payment_method) === 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('payment_method', $expense->payment_method) === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Upload Nota</label>
                    <input type="file" name="receipt_image" accept="image/jpg,image/jpeg,image/png"
                           class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm file:mr-2 file:rounded-md file:border-0 file:bg-orange-50 file:px-2.5 file:py-1 file:text-xs file:font-semibold file:text-orange-700 hover:file:bg-orange-100 focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                    @if($expense->receipt_image)
                    <div class="mt-1.5 flex items-center gap-2">
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($expense->receipt_image) }}"
                           target="_blank"
                           class="text-xs text-orange-600 hover:underline">
                            <i class="fas fa-image mr-0.5"></i> Lihat Nota
                        </a>
                        <label class="flex items-center gap-1 text-xs text-gray-400">
                            <input type="checkbox" name="delete_receipt" value="1" class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                            Hapus
                        </label>
                    </div>
                    @endif
                    @error('receipt_image') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">{{ old('notes', $expense->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <a href="{{ route('pos.expenses.index') }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-orange-500 to-rose-500 px-4 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
                    {{ $isEditing ? 'Simpan Perubahan' : 'Catat Biaya' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
