@extends('layouts.app')
@section('title', 'Edit Produk')
@section('breadcrumb', 'Edit Produk')

@section('content')
<div class="mx-auto max-w-3xl space-y-4">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('pos.products.index') }}"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-gray-900">Edit Produk</h1>
            <p class="text-xs text-gray-400 truncate">{{ $product->name }}</p>
        </div>
        <a href="{{ route('pos.products.show', $product) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
            <i class="fas fa-eye text-[10px]"></i> Lihat Detail
        </a>
    </div>

    <form action="{{ route('pos.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf
        @method('PUT')

        {{-- Informasi Produk --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    <i class="fas fa-info-circle mr-1.5 text-orange-500"></i> Informasi Produk
                </p>
            </div>
            <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-2">
                {{-- Nama --}}
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required autofocus
                        placeholder="Contoh: Indomie Goreng"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('name') border-red-400 @enderror" />
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- SKU --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                        placeholder="SKU-XXXXX"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('sku') border-red-400 @enderror" />
                    @error('sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Barcode --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                        placeholder="Otomatis jika kosong"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('barcode') border-red-400 @enderror" />
                    @error('barcode') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('category_id') border-red-400 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Satuan</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}"
                        placeholder="pcs, kg, liter, dll"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition" />
                    @error('unit') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi produk (opsional)"
                        class="w-full resize-none rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Harga & Stok --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    <i class="fas fa-tags mr-1.5 text-orange-500"></i> Harga & Stok
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4 md:grid-cols-4">
                {{-- Harga Jual --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Harga Jual <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rp</span>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" step="100"
                            placeholder="0"
                            class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 font-mono text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('price') border-red-400 @enderror" />
                    </div>
                    @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- HPP --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Harga Pokok (HPP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rp</span>
                        <input type="number" name="cost" value="{{ old('cost', $product->cost) }}" min="0" step="100"
                            placeholder="0"
                            class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 font-mono text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition" />
                    </div>
                    <p class="mt-1 text-[10px] text-gray-400">Untuk perhitungan profit</p>
                </div>

                {{-- Stok --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Stok Saat Ini</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                        placeholder="0"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('stock') border-red-400 @enderror" />
                    @error('stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Stok Minimum --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Stok Minimum</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}" min="0"
                        placeholder="5"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition" />
                    <p class="mt-1 text-[10px] text-gray-400">Alert jika stok di bawah ini</p>
                </div>
            </div>
        </div>

        {{-- Gambar & Status --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    <i class="fas fa-image mr-1.5 text-orange-500"></i> Gambar & Status
                </p>
            </div>
            <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start">
                {{-- Image --}}
                <div x-data="{ preview: '{{ $product->image_url ?? '' }}' }" class="flex items-start gap-3">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50">
                        <template x-if="!preview"><i class="fas fa-image text-xl text-gray-300"></i></template>
                        <template x-if="preview"><img :src="preview" class="h-full w-full object-cover" /></template>
                    </div>
                    <div>
                        <label class="block cursor-pointer">
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-upload text-[10px]"></i> Ganti Gambar
                            </span>
                            <input type="file" name="image" accept="image/*" class="hidden"
                                @change="const f=$event.target.files[0];if(f){const r=new FileReader();r.onload=e=>preview=e.target.result;r.readAsDataURL(f)}" />
                        </label>
                        <p class="mt-1.5 text-[10px] text-gray-400">JPG, PNG, WEBP · max 2MB</p>
                        @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex-1 sm:border-l sm:border-gray-100 sm:pl-4">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400" />
                        <div>
                            <p class="text-xs font-semibold text-gray-800">Produk Aktif</p>
                            <p class="text-[10px] text-gray-400">Tampil di kasir dan dapat dijual</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('pos.products.index') }}"
                class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-600 transition-colors">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
