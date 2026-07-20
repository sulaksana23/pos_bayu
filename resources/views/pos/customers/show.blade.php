@extends('layouts.app')
@section('title', $customer->name)
@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.customers.index') }}" class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-600 font-bold text-sm">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-base font-bold text-gray-900">{{ $customer->name }}</h1>
                <p class="text-xs text-gray-400">Pelanggan sejak {{ $customer->created_at->isoFormat('D MMMM YYYY') }}</p>
            </div>
        </div>
        <a href="{{ route('pos.customers.edit', $customer) }}"
            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 hover:shadow-lg transition">
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>

    {{-- Info + Stats --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Info --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Informasi</h3>
            @if($customer->phone)
            <div class="flex items-center gap-2 text-sm">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100"><i class="fas fa-phone text-blue-600 text-xs"></i></div>
                <span class="text-gray-700">{{ $customer->phone }}</span>
            </div>
            @endif
            @if($customer->email)
            <div class="flex items-center gap-2 text-sm">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100"><i class="fas fa-envelope text-purple-600 text-xs"></i></div>
                <span class="text-gray-700">{{ $customer->email }}</span>
            </div>
            @endif
            @if($customer->address)
            <div class="flex items-start gap-2 text-sm">
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100"><i class="fas fa-map-marker-alt text-emerald-600 text-xs"></i></div>
                <span class="text-gray-700">{{ $customer->address }}</span>
            </div>
            @endif
            @if($customer->notes)
            <div class="rounded-lg bg-amber-50 border border-amber-100 p-2.5 text-xs text-amber-800">
                <i class="fas fa-sticky-note mr-1"></i>{{ $customer->notes }}
            </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="lg:col-span-2 grid grid-cols-3 gap-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm text-center">
                <p class="text-xs text-gray-400 uppercase">Total Transaksi</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_transactions']) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm text-center">
                <p class="text-xs text-emerald-700 uppercase">Total Belanja</p>
                <p class="mt-1 text-xl font-bold text-emerald-700">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm text-center">
                <p class="text-xs text-blue-700 uppercase">Rata-rata</p>
                <p class="mt-1 text-xl font-bold text-blue-700">Rp {{ number_format($stats['avg_transaction'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Riwayat Transaksi --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-900">Riwayat Transaksi Terakhir</h3>
        </div>
        @if($customer->transactions->isEmpty())
        <div class="px-4 py-10 text-center text-sm text-gray-400">Belum ada transaksi</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Invoice</th>
                        <th class="px-4 py-2.5 text-left">Tanggal</th>
                        <th class="px-4 py-2.5 text-right">Total</th>
                        <th class="px-4 py-2.5 text-center">Pembayaran</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customer->transactions as $trx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2.5 font-mono text-gray-600">{{ $trx->invoice_no }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $trx->created_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700">{{ strtoupper($trx->payment_method) }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if($trx->status === 'completed')
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Selesai</span>
                            @else
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-600">{{ $trx->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
