@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="space-y-3" x-data="{
    view: localStorage.getItem('products_view') || 'table',
    setView(v) { this.view = v; localStorage.setItem('products_view', v); }
}">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Produk</h1>
            <p class="text-xs text-gray-400">{{ $products->total() }} produk terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Toggle View --}}
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
                <button @click="setView('table')"
                    :class="view === 'table' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                    <i class="fas fa-table mr-1"></i> Tabel
                </button>
                <button @click="setView('grid')"
                    :class="view === 'grid' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                    <i class="fas fa-th-large mr-1"></i> Grid
                </button>
            </div>
            <a href="{{ route('pos.products.create') }}"
                class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs text-emerald-800">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100">
                    <i class="fas fa-box text-sm text-blue-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Total</p>
                    <p class="text-lg font-bold text-gray-900">{{ $products->total() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <i class="fas fa-check-circle text-sm text-emerald-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Aktif</p>
                    <p class="text-lg font-bold text-gray-900">{{ $products->getCollection()->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-100">
                    <i class="fas fa-exclamation-triangle text-sm text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Stok Rendah</p>
                    <p class="text-lg font-bold text-gray-900">{{ $products->getCollection()->filter(fn($p) => $p->stock > 0 && $p->stock <= $p->min_stock)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100">
                    <i class="fas fa-times-circle text-sm text-red-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Stok Habis</p>
                    <p class="text-lg font-bold text-gray-900">{{ $products->getCollection()->where('stock', '<=', 0)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('pos.products.index') }}"
        class="flex flex-wrap items-center gap-2 rounded-2xl border border-gray-100 bg-white px-3 py-2.5 shadow-sm">
        <div class="relative min-w-0 flex-1" style="min-width:160px">
            <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-xs text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Nama, SKU, Barcode..."
                class="w-full rounded-lg border border-gray-200 py-1.5 pr-3 pl-9 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20" />
        </div>
        <select name="category_id"
            class="rounded-lg border border-gray-200 py-1.5 px-3 text-sm text-gray-600 focus:border-orange-400 focus:outline-none">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="stock_status"
            class="rounded-lg border border-gray-200 py-1.5 px-3 text-sm text-gray-600 focus:border-orange-400 focus:outline-none">
            <option value="">Semua Stok</option>
            <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Rendah</option>
            <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis</option>
        </select>
        <select name="is_active"
            class="rounded-lg border border-gray-200 py-1.5 px-3 text-sm text-gray-600 focus:border-orange-400 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit"
            class="rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-orange-600 transition">
            <i class="fas fa-search mr-1"></i> Cari
        </button>
        @if (request()->hasAny(['search','category_id','stock_status','is_active']))
            <a href="{{ route('pos.products.index') }}"
                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
        @endif
    </form>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-gray-100 bg-white px-4 py-12 text-center shadow-sm">
            <i class="fas fa-box-open mb-2 block text-3xl text-gray-200"></i>
            <p class="text-sm font-medium text-gray-400">Belum ada produk ditemukan</p>
            <a href="{{ route('pos.products.create') }}"
                class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-orange-500 px-3 py-2 text-xs font-bold text-white">
                <i class="fas fa-plus"></i> Tambah Produk
            </a>
        </div>
    @else

    {{-- TABLE VIEW --}}
    <div x-show="view === 'table'" x-cloak>
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                        <tr>
                            <th class="px-3 py-3 text-left">Produk</th>
                            <th class="hidden px-3 py-3 text-left sm:table-cell">SKU</th>
                            <th class="hidden px-3 py-3 text-left md:table-cell">Kategori</th>
                            <th class="px-3 py-3 text-right">Harga</th>
                            <th class="px-3 py-3 text-center">Stok</th>
                            <th class="hidden px-3 py-3 text-center sm:table-cell">Status</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($products as $product)
                            @php
                                $stockBg = $product->stock <= 0 ? 'bg-red-100 text-red-700' : ($product->stock <= $product->min_stock ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-9 w-9 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                            @if ($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover" loading="lazy" />
                                            @else
                                                <div class="flex h-full w-full items-center justify-center">
                                                    <i class="fas fa-box text-gray-300"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-semibold text-gray-900">{{ $product->name }}</p>
                                            @if ($product->unit)
                                                <p class="text-[10px] text-gray-400">{{ $product->unit }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden px-3 py-2.5 sm:table-cell">
                                    <p class="font-mono text-xs text-gray-600">{{ $product->sku }}</p>
                                    @if ($product->barcode)
                                        <p class="font-mono text-[10px] text-gray-400">{{ $product->barcode }}</p>
                                    @endif
                                </td>
                                <td class="hidden px-3 py-2.5 md:table-cell">
                                    @if ($product->category)
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600">
                                            <i class="{{ $product->category->icon ?? 'fas fa-tag' }} text-[9px]"></i>
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <p class="text-xs font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    @if ($product->cost)
                                        <p class="text-[10px] text-gray-400">HPP {{ number_format($product->cost, 0, ',', '.') }}</p>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="rounded-lg px-2 py-0.5 text-xs font-bold {{ $stockBg }}">
                                        {{ $product->stock }}
                                    </span>
                                    @if ($product->min_stock)
                                        <p class="mt-0.5 text-[10px] text-gray-400">min {{ $product->min_stock }}</p>
                                    @endif
                                </td>
                                <td class="hidden px-3 py-2.5 text-center sm:table-cell">
                                    @if ($product->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('pos.products.show', $product) }}"
                                            class="rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-500 hover:bg-gray-50 transition" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('pos.products.edit', $product) }}"
                                            class="rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-amber-600 hover:bg-amber-50 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pos.products.barcode', $product) }}" target="_blank"
                                            class="hidden rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-purple-600 hover:bg-purple-50 transition sm:block" title="Barcode">
                                            <i class="fas fa-barcode"></i>
                                        </a>
                                        <form method="POST" action="{{ route('pos.products.destroy', $product) }}"
                                            onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 px-2 py-1.5 text-xs text-red-600 hover:bg-red-50 transition" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $products->links() }}</div>
            @endif
        </div>
    </div>

    {{-- GRID VIEW --}}
    <div x-show="view === 'grid'" x-cloak>
        <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($products as $product)
                @php
                    $stockBg = $product->stock <= 0 ? 'bg-red-100 text-red-700' : ($product->stock <= $product->min_stock ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                @endphp
                <div class="group rounded-2xl border border-gray-100 bg-white shadow-sm transition-all hover:shadow-md hover:border-gray-200">
                    {{-- Image --}}
                    <div class="relative aspect-square overflow-hidden rounded-t-2xl bg-gray-50">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy" />
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <i class="fas fa-box text-3xl text-gray-200"></i>
                            </div>
                        @endif
                        {{-- Status badge --}}
                        <div class="absolute top-2 right-2">
                            @if (!$product->is_active)
                                <span class="rounded-full bg-gray-800/70 px-1.5 py-0.5 text-[9px] font-bold text-white">Nonaktif</span>
                            @endif
                        </div>
                        {{-- Stock badge --}}
                        <div class="absolute bottom-2 left-2">
                            <span class="rounded-lg px-1.5 py-0.5 text-[10px] font-bold shadow-sm {{ $stockBg }}">
                                {{ $product->stock }} {{ $product->unit ?? 'pcs' }}
                            </span>
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="p-2.5">
                        <p class="line-clamp-2 text-[11px] font-semibold leading-tight text-gray-800">{{ $product->name }}</p>
                        @if ($product->category)
                            <p class="mt-0.5 text-[10px] text-gray-400">{{ $product->category->name }}</p>
                        @endif
                        <p class="mt-1.5 text-sm font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        {{-- Actions --}}
                        <div class="mt-2 flex items-center gap-1 border-t border-gray-50 pt-2">
                            <a href="{{ route('pos.products.edit', $product) }}"
                                class="flex-1 rounded-lg border border-gray-200 py-1 text-center text-[10px] font-semibold text-gray-600 hover:bg-gray-50 transition">
                                <i class="fas fa-edit mr-0.5"></i> Edit
                            </a>
                            <a href="{{ route('pos.products.show', $product) }}"
                                class="rounded-lg border border-gray-200 px-2 py-1 text-[10px] text-gray-400 hover:bg-gray-50 transition">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('pos.products.destroy', $product) }}"
                                onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="rounded-lg border border-red-200 px-2 py-1 text-[10px] text-red-500 hover:bg-red-50 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($products->hasPages())
            <div class="mt-3 rounded-2xl border border-gray-100 bg-white px-4 py-3 shadow-sm">{{ $products->links() }}</div>
        @endif
    </div>

    @endif
</div>
@endsection
