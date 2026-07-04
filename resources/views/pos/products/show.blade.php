@extends ('layouts.app')
@section ('title', 'Detail Produk')

@section ('content')
    <div class="mx-auto max-w-5xl space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a
                    href="{{ route('pos.products.index') }}"
                    class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100"
                >
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $product->sku }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('pos.products.barcode', $product) }}"
                    target="_blank"
                    class="rounded-lg bg-purple-600 px-4 py-2 font-medium text-white transition hover:bg-purple-700"
                >
                    <i class="fas fa-barcode mr-2"></i>
                    Print Barcode
                </a>
                <a
                    href="{{ route('pos.products.edit', $product) }}"
                    class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700"
                >
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left Column - Product Info --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Basic Information --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Informasi Produk</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="mb-1 text-sm text-gray-600">Nama Produk</p>
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm text-gray-600">Kategori</p>
                            @if ($product->category)
                                <span
                                    class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1 font-medium text-gray-700"
                                >
                                    <i class="{{ $product->category->icon }} text-sm"></i>
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                        <div>
                            <p class="mb-1 text-sm text-gray-600">SKU</p>
                            <p class="font-mono text-gray-900">{{ $product->sku }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm text-gray-600">Barcode</p>
                            <p class="font-mono text-gray-900">{{ $product->barcode ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm text-gray-600">Satuan</p>
                            <p class="text-gray-900">{{ $product->unit ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm text-gray-600">Status</p>
                            @if ($product->is_active)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-600"
                                >
                                    <i class="fas fa-pause-circle"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                    @if ($product->description)
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <p class="mb-1 text-sm text-gray-600">Deskripsi</p>
                            <p class="text-gray-900">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>

                {{-- Pricing --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Harga & Profit</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="rounded-lg bg-green-50 p-4 text-center">
                            <p class="mb-1 text-sm text-green-600">Harga Jual</p>
                            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-4 text-center">
                            <p class="mb-1 text-sm text-blue-600">HPP</p>
                            <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($product->cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-4 text-center">
                            <p class="mb-1 text-sm text-purple-600">Profit</p>
                            @php
                            $profit = $product->price - ($product->cost ?? 0);
                            $profitPercent = $product->cost > 0 ? ($profit / $product->cost) * 100 : 0;
                        @endphp
                            <p class="text-2xl font-bold text-purple-700">Rp {{ number_format($profit, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-purple-600">{{ number_format($profitPercent, 1) }}%</p>
                        </div>
                    </div>
                </div>

                {{-- Stock Information --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Informasi Stok</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="mb-1 text-sm text-gray-600">Stok Saat Ini</p>
                            @php
                            $stockClass = 'text-gray-900';
                            if($product->stock <= 0) $stockClass = 'text-red-600';
                            elseif($product->stock <= $product->min_stock) $stockClass = 'text-yellow-600';
                        @endphp
                            <p
                                class="text-3xl font-bold {{ $stockClass }}"
                            >{{ $product->stock }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="mb-1 text-sm text-gray-600">Stok Minimum</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $product->min_stock ?? 0 }}</p>
                        </div>
                    </div>
                    @if ($product->stock <= $product->min_stock)
                        <div
                            class="mt-4 flex items-start gap-2 rounded-lg border border-yellow-200 bg-yellow-50 p-3"
                        >
                            <i class="fas fa-exclamation-triangle mt-0.5 text-yellow-600"></i>
                            <div>
                                <p class="font-semibold text-yellow-900">Peringatan Stok</p>
                                <p class="text-sm text-yellow-700">Stok produk sudah mencapai atau di bawah batas minimum. Segera lakukan restocking!</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column - Image & Quick Actions --}}
            <div class="space-y-6">
                {{-- Product Image --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Gambar Produk</h2>
                    <div class="aspect-square overflow-hidden rounded-lg bg-gray-100">
                        @if ($product->image_url)
                            <img
                                src="{{ $product->image_url }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover"
                            />
                        @else
                            <div
                                class="flex h-full w-full flex-col items-center justify-center text-gray-400"
                            >
                                <i class="fas fa-image mb-2 text-6xl"></i>
                                <p class="text-sm">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Statistik Penjualan</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Terjual</span>
                            <span class="font-bold text-gray-900">0 pcs</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Pendapatan</span>
                            <span class="font-bold text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Profit</span>
                            <span class="font-bold text-gray-900">Rp 0</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                    <div class="space-y-2">
                        <a
                            href="{{ route('pos.products.edit', $product) }}"
                            class="flex items-center gap-3 rounded-lg p-3 transition hover:bg-gray-50"
                        >
                            <i class="fas fa-edit text-blue-600"></i>
                            <span class="text-sm font-medium text-gray-900">Edit Produk</span>
                        </a>
                        <a
                            href="{{ route('pos.products.barcode', $product) }}"
                            target="_blank"
                            class="flex items-center gap-3 rounded-lg p-3 transition hover:bg-gray-50"
                        >
                            <i class="fas fa-barcode text-purple-600"></i>
                            <span class="text-sm font-medium text-gray-900">Print Barcode</span>
                        </a>
                        <button
                            onclick="
                                if (confirm('Yakin ingin menghapus produk ini?'))
                                    document.getElementById('delete-form').submit();
                            "
                            class="flex w-full items-center gap-3 rounded-lg p-3 text-left transition hover:bg-red-50"
                        >
                            <i class="fas fa-trash text-red-600"></i>
                            <span class="text-sm font-medium text-red-600">Hapus Produk</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Delete Form --}}
    <form
        id="delete-form"
        action="{{ route('pos.products.destroy', $product) }}"
        method="POST"
        class="hidden"
    >
        @csrf
        @method ('DELETE')
    </form>
@endsection
