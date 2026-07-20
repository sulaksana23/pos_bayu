@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('breadcrumb', 'Edit Kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form action="{{ route('pos.categories.update', $category) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                           required>
                    @error('name') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Warna</label>
                    <input type="color" name="color" value="{{ old('color', $category->color ?? '#f97316') }}"
                           class="h-9 w-full rounded-lg border border-gray-200 cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Icon (Font Awesome)</label>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                           placeholder="fa-box"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                    <p class="text-[10px] text-gray-400 mt-0.5">Contoh: fa-box, fa-mug-hot, fa-tshirt</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                           required>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                        <span class="text-xs font-semibold text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <a href="{{ route('pos.categories.index') }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-orange-500 to-rose-500 px-4 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
