@php
    $isEdit = $mode === 'edit' && isset($product);
    $p = $product ?? new \App\Models\Product();
@endphp
<form
    method="POST"
    action="{{ $isEdit ? route('pos.inventory.update', $p) : route('pos.inventory.store') }}"
    class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm"
>
    @csrf
    @if ($isEdit) @method ('PUT')@endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-gray-700">Nama Produk *</label>
            <input
                name="name"
                required
                value="{{ old('name', $p->name) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
            />
            @error ('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">SKU *</label>
            <input
                name="sku"
                required
                value="{{ old('sku', $p->sku) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
            />
            @error ('sku')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">Barcode</label>
            <input
                name="barcode"
                value="{{ old('barcode', $p->barcode) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
            />
            @error ('barcode')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">Kategori</label>
            <select
                name="category_id"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
            >
                <option value="">— Tanpa kategori —</option>
                @foreach ($categories as $c)
                    <option
                        value="{{ $c->id }}"
                        @selected (old('category_id', $p->category_id) == $c->id)
                    >
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">Satuan</label>
            <input
                name="unit"
                required
                value="{{ old('unit', $p->unit ?? 'pcs') }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
            />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">Harga Jual (Rp) *</label>
            <input
                type="number"
                name="price"
                required
                min="0"
                step="100"
                value="{{ old('price', $p->price ?? 0) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-500 focus:outline-none"
            />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700">Harga Modal (Rp)</label>
            <input
                type="number"
                name="cost"
                min="0"
                step="100"
                value="{{ old('cost', $p->cost) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-500 focus:outline-none"
            />
        </div>
        @if (!$isEdit)
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Stok Awal *</label>
                <input
                    type="number"
                    name="stock"
                    required
                    min="0"
                    value="{{ old('stock', 0) }}"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-500 focus:outline-none"
                />
            </div>
        @endif
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-700"
                >Minimum Stok (alert)</label
            >
            <input
                type="number"
                name="min_stock"
                min="0"
                required
                value="{{ old('min_stock', $p->min_stock ?? 0) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm focus:border-orange-500 focus:outline-none"
            />
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-gray-700"
                >URL Gambar (opsional)</label
            >
            <input
                name="image_url"
                type="url"
                value="{{ old('image_url', $p->image_url) }}"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
            />
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-gray-700">Deskripsi</label>
            <textarea
                name="description"
                rows="3"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                >{{ old('description', $p->description) }}</textarea
            >
        </div>
        <div class="sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    checked
                    class="rounded text-orange-500"
                />
                <span>Aktif (bisa dijual di kasir)</span>
            </label>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
        <a
            href="{{ route('pos.inventory.index') }}"
            class="rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >Batal</a
        >
        <button
            class="rounded-lg bg-gradient-to-r from-orange-500 to-rose-500 px-5 py-2 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition hover:shadow-xl"
        >
            <i class="fas fa-save mr-1"></i> Simpan
        </button>
    </div>
</form>
