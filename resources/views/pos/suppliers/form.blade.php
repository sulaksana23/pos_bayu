@php
    $isEditing = $supplier->exists;
@endphp
@extends('layouts.app')
@section('title', $isEditing ? 'Edit Supplier' : 'Tambah Supplier')
@section('breadcrumb', $isEditing ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form action="{{ $isEditing ? route('pos.suppliers.update', $supplier) : route('pos.suppliers.store') }}"
              method="POST">
            @csrf
            @if($isEditing) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                           required>
                    @error('name') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan</label>
                    <input type="text" name="company" value="{{ old('company', $supplier->company) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">NPWP</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama PIC</label>
                    <input type="text" name="pic_name" value="{{ old('pic_name', $supplier->pic_name) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Telepon PIC</label>
                    <input type="text" name="pic_phone" value="{{ old('pic_phone', $supplier->pic_phone) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" rows="2"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">{{ old('address', $supplier->address) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">{{ old('notes', $supplier->notes) }}</textarea>
                </div>

                @if($isEditing)
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                        <span class="text-xs font-semibold text-gray-700">Supplier Aktif</span>
                    </label>
                </div>
                @endif
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <a href="{{ route('pos.suppliers.index') }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-orange-500 to-rose-500 px-4 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
                    {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Supplier' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
