@extends ('layouts.app')
@section ('title', 'Edit Produk')

@section ('content')
    <div class="mx-auto max-w-4xl space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('pos.products.index') }}"
                class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100"
            >
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $product->name }}</p>
            </div>
        </div>

        {{-- Form --}}
        <form
            action="{{ route('pos.products.update', $product) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method ('PUT')

            {{-- Product Information --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Informasi Produk
                </h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- Product Name --}}
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $product->name) }}"
                            required
                            placeholder="Contoh: Indomie Goreng"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        />
                        @error ('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SKU --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="sku"
                            value="{{ old('sku', $product->sku) }}"
                            required
                            placeholder="SKU-XXXXX"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sku') border-red-500 @enderror"
                        />
                        @error ('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Barcode
                        </label>
                        <input
                            type="text"
                            name="barcode"
                            value="{{ old('barcode', $product->barcode) }}"
                            placeholder="Barcode produk"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('barcode') border-red-500 @enderror"
                        />
                        @error ('barcode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error ('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700"> Satuan </label>
                        <input
                            type="text"
                            name="unit"
                            value="{{ old('unit', $product->unit) }}"
                            placeholder="pcs, kg, liter, dll"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('unit') border-red-500 @enderror"
                        />
                        @error ('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Deskripsi
                        </label>
                        <textarea
                            name="description"
                            rows="3"
                            placeholder="Deskripsi produk (opsional)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                            >{{ old('description', $product->description) }}</textarea
                        >
                        @error ('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                    Harga & Biaya
                </h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- Selling Price --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-500"
                                >Rp</span
                            >
                            <input
                                type="number"
                                name="price"
                                value="{{ old('price', $product->price) }}"
                                required
                                min="0"
                                step="100"
                                placeholder="0"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                            />
                        </div>
                        @error ('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cost Price --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Harga Pokok (HPP)
                        </label>
                        <div class="relative">
                            <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-500"
                                >Rp</span
                            >
                            <input
                                type="number"
                                name="cost"
                                value="{{ old('cost', $product->cost) }}"
                                min="0"
                                step="100"
                                placeholder="0"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('cost') border-red-500 @enderror"
                            />
                        </div>
                        @error ('cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Untuk perhitungan profit</p>
                    </div>
                </div>
            </div>

            {{-- Stock Management --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <i class="fas fa-warehouse text-yellow-600"></i>
                    Manajemen Stok
                </h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- Current Stock --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Stok Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            value="{{ old('stock', $product->stock) }}"
                            required
                            min="0"
                            placeholder="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-500 @enderror"
                        />
                        @error ('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Min Stock --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Stok Minimum
                        </label>
                        <input
                            type="number"
                            name="min_stock"
                            value="{{ old('min_stock', $product->min_stock) }}"
                            min="0"
                            placeholder="5"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('min_stock') border-red-500 @enderror"
                        />
                        @error ('min_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Notifikasi jika stok mencapai batas ini</p>
                    </div>
                </div>
            </div>

            {{-- Product Image --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <i class="fas fa-image text-purple-600"></i>
                    Gambar Produk
                </h2>

                <div x-data="{ preview: '{{ $product->image_url }}' }">
                    <div class="flex items-start gap-4">
                        {{-- Image Preview --}}
                        <div
                            class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-gray-300 bg-gray-50"
                        >
                            <template x-if="!preview">
                                <i class="fas fa-image text-3xl text-gray-400"></i>
                            </template>
                            <template x-if="preview">
                                <img :src="preview" class="h-full w-full object-cover" />
                            </template>
                        </div>

                        {{-- Upload Button --}}
                        <div class="flex-1">
                            <label class="block">
                                <span
                                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 transition hover:bg-gray-50"
                                >
                                    <i class="fas fa-upload"></i>
                                    <span class="text-sm font-medium text-gray-700"
                                        >Ganti Gambar</span
                                    >
                                </span>
                                <input
                                    type="file"
                                    name="image"
                                    accept="image/*"
                                    class="hidden"
                                    @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = e => (preview = e.target.result);
                                            reader.readAsDataURL(file);
                                        }
                                    "
                                />
                            </label>
                            <p class="mt-2 text-xs text-gray-500">Format: JPG, PNG, WEBP (max 2MB)</p>
                            @if ($product->image_url)
                                <p class="mt-1 text-xs text-green-600">Gambar saat ini: {{ basename($product->image_url) }}</p>
                            @endif
                            @error ('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <i class="fas fa-toggle-on text-gray-600"></i>
                    Status Produk
                </h2>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <div>
                        <p class="font-medium text-gray-900">Produk Aktif</p>
                        <p class="text-sm text-gray-600">Produk akan tampil di kasir dan dapat dijual</p>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <a
                    href="{{ route('pos.products.show', $product) }}"
                    class="font-medium text-blue-600 hover:text-blue-700"
                >
                    <i class="fas fa-eye mr-1"></i>
                    Lihat Detail
                </a>
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('pos.products.index') }}"
                        class="rounded-lg border border-gray-300 px-6 py-2.5 font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 font-semibold text-white shadow-md transition hover:bg-blue-700"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
