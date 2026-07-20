@extends('layouts.app')
@section('title', 'Laporan Produk')
@section('content')
<div class="space-y-4">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Laporan Produk Terlaris</h1>
            <p class="mt-0.5 text-xs text-gray-400">{{ $startDate }} s/d {{ $endDate }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-2 rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
        <div>
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">Dari</label>
            <input type="date" name="start_date" value="{{ $startDate }}"
                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:border-orange-500 focus:outline-none" />
        </div>
        <div>
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">Sampai</label>
            <input type="date" name="end_date" value="{{ $endDate }}"
                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:border-orange-500 focus:outline-none" />
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
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Item Terjual</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($summary['total_items'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Total Pendapatan</p>
            <p class="mt-1 text-xl font-bold text-emerald-700">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Produk Terjual</p>
            <p class="mt-1 text-xl font-bold text-blue-700">{{ number_format($summary['unique_products'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Tabel Top Produk --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-900">Produk Terlaris</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5 text-left w-8">#</th>
                        <th class="px-4 py-2.5 text-left">Produk</th>
                        <th class="px-4 py-2.5 text-left">Kategori</th>
                        <th class="px-4 py-2.5 text-right">Qty Terjual</th>
                        <th class="px-4 py-2.5 text-right">Pendapatan</th>
                        <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topProducts as $i => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold
                                {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-200 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $i + 1 }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            <p class="font-semibold text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-[10px] text-gray-400">SKU: {{ $item->product_sku ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-2.5 text-gray-600">{{ $item->category_name ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">{{ number_format($item->total_qty) }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-emerald-600">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-500">Rp {{ $item->total_qty > 0 ? number_format($item->total_revenue / $item->total_qty, 0, ',', '.') : 0 }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada data penjualan produk pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
