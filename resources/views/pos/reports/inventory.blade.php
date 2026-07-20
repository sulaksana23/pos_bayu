@extends('layouts.app')
@section('title', 'Laporan Inventori')
@section('content')
<div class="space-y-4">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Laporan Inventori</h1>
            <p class="mt-0.5 text-xs text-gray-400">Status stok semua produk</p>
        </div>
        <a href="{{ route('pos.inventory.index') }}"
            class="inline-flex items-center gap-1.5 rounded-xl border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-100 transition">
            <i class="fas fa-boxes-stacked"></i> Kelola Stok
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-2 rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
        <div>
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">Status Stok</label>
            <select name="stock" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:border-orange-500 focus:outline-none">
                <option value="all" {{ $stockFilter === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="low" {{ $stockFilter === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="out" {{ $stockFilter === 'out' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">Kategori</label>
            <select name="category" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:border-orange-500 focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-semibold text-white hover:bg-orange-500 transition">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
        </div>
    </form>

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Produk</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($summary['total_products']) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Stok Menipis</p>
            <p class="mt-1 text-xl font-bold text-amber-700">{{ number_format($summary['low_stock']) }}</p>
        </div>
        <div class="rounded-2xl border border-red-100 bg-red-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-red-600">Stok Habis</p>
            <p class="mt-1 text-xl font-bold text-red-700">{{ number_format($summary['out_of_stock']) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Nilai Stok</p>
            <p class="mt-1 text-lg font-bold text-emerald-700">Rp {{ number_format($summary['total_value'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tabel Stok --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-900">Daftar Stok Produk</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Produk</th>
                        <th class="px-4 py-2.5 text-left">Kategori</th>
                        <th class="px-4 py-2.5 text-right">Harga</th>
                        <th class="px-4 py-2.5 text-right">Modal</th>
                        <th class="px-4 py-2.5 text-center">Stok</th>
                        <th class="px-4 py-2.5 text-center">Min. Stok</th>
                        <th class="px-4 py-2.5 text-right">Nilai Stok</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $p)
                    <tr class="hover:bg-gray-50 transition {{ $p->stock === 0 ? 'bg-red-50/40' : ($p->is_low_stock ? 'bg-amber-50/40' : '') }}">
                        <td class="px-4 py-2.5">
                            <p class="font-semibold text-gray-800">{{ $p->name }}</p>
                            <p class="text-[10px] text-gray-400">SKU: {{ $p->sku ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-2.5 text-gray-600">{{ $p->category?->name ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-500">Rp {{ number_format($p->cost ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="font-bold {{ $p->stock === 0 ? 'text-red-600' : ($p->is_low_stock ? 'text-amber-600' : 'text-gray-800') }}">
                                {{ number_format($p->stock) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center text-gray-500">{{ $p->min_stock }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-600">Rp {{ number_format($p->stock * ($p->cost ?? $p->price), 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-center">
                            @if($p->stock === 0)
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700">Habis</span>
                            @elseif($p->is_low_stock)
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Menipis</span>
                            @else
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Normal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">Tidak ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $products->links() }}</div>
    </div>
</div>
@endsection
