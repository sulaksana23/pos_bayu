@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('breadcrumb', 'Laporan Harian')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Laporan Harian</h1>
            <p class="text-xs text-gray-400">{{ $date->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('pos.accounting.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Summary --}}
    @php
        $totalSales = $transactions->sum('total');
        $totalCount = $transactions->count();
        $totalCash = $transactions->where('payment_method', 'cash')->sum('paid');
        $totalNonCash = $transactions->whereIn('payment_method', ['qris','transfer','wallet'])->sum('paid');
    @endphp
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Penjualan</p>
            <p class="mt-1 text-xl font-bold text-emerald-700">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Transaksi</p>
            <p class="mt-1 text-xl font-bold text-blue-700">{{ $totalCount }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Tunai</p>
            <p class="mt-1 text-xl font-bold text-emerald-600">Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Non-Tunai</p>
            <p class="mt-1 text-xl font-bold text-blue-600">Rp {{ number_format($totalNonCash, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-900">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td class="font-mono text-xs text-gray-600">{{ $t->invoice_no }}</td>
                        <td class="text-xs text-gray-500 whitespace-nowrap">{{ $t->created_at->format('H:i') }}</td>
                        <td class="text-xs text-gray-700">{{ $t->cashier?->name ?? '-' }}</td>
                        <td class="text-xs text-gray-700">{{ $t->customer?->name ?? '-' }}</td>
                        <td class="text-right text-xs text-gray-600">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                        <td class="text-right text-xs font-semibold text-gray-900">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700">
                                {{ strtoupper($t->payment_method) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            <i class="fas fa-receipt text-2xl text-gray-200 mb-2 block"></i>
                            Tidak ada transaksi pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
