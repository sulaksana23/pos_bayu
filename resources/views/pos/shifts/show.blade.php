@extends('layouts.app')
@section('title', 'Shift #' . $shift->id)
@section('breadcrumb', 'Detail Shift')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('pos.shifts.index') }}"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-gray-900">Shift #{{ $shift->id }}</h1>
                    @if ($shift->status === 'open')
                        <span class="fb-badge fb-badge-green">
                            <span class="mr-1 h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span> Aktif
                        </span>
                    @else
                        <span class="fb-badge fb-badge-gray">Tutup</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400">
                    {{ $shift->user->name }} &middot;
                    {{ $shift->opened_at->translatedFormat('d M Y H:i') }}
                    @if($shift->closed_at) → {{ $shift->closed_at->format('H:i') }} @else → (sedang berjalan) @endif
                </p>
            </div>
        </div>
        @if ($shift->status === 'open' && ($shift->user_id === auth()->id() || auth()->user()->isAdmin()))
            @include('pos.shifts._close_modal', ['shift' => $shift])
            <button onclick="document.getElementById('closeShift{{ $shift->id }}').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                <i class="fas fa-stop"></i> Tutup Shift
            </button>
        @endif
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Opening Cash</p>
            <p class="mt-1.5 font-mono text-base font-bold text-gray-900">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Total Sales</p>
            <p class="mt-1.5 font-mono text-base font-bold text-emerald-700">Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Cash</p>
            <p class="mt-1.5 font-mono text-base font-bold text-blue-700">Rp {{ number_format($shift->total_cash, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-violet-200 bg-violet-50 p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-violet-600">Non-Cash</p>
            <p class="mt-1.5 font-mono text-base font-bold text-violet-700">Rp {{ number_format($shift->total_non_cash, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Transaksi</p>
            <p class="mt-1.5 font-mono text-base font-bold text-amber-700">{{ $shift->transaction_count }}</p>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-800">Transaksi dalam Shift Ini</h3>
            <span class="fb-badge fb-badge-gray">{{ $shift->transactions->count() }} transaksi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Kasir</th>
                        <th class="text-right">Total</th>
                        <th>Metode</th>
                        <th>Waktu</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shift->transactions as $t)
                        <tr>
                            <td class="font-mono text-xs">{{ $t->invoice_no }}</td>
                            <td class="text-xs">{{ $t->customer?->name ?? 'Umum' }}</td>
                            <td class="text-xs">{{ $t->cashier?->name }}</td>
                            <td class="text-right font-mono text-xs font-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $pmBadge = match($t->payment_method) {
                                        'cash'     => 'fb-badge-green',
                                        'qris'     => 'fb-badge-blue',
                                        'transfer' => 'fb-badge fb-badge-purple',
                                        default    => 'fb-badge-gray',
                                    };
                                @endphp
                                <span class="fb-badge {{ $pmBadge }}">{{ strtoupper($t->payment_method) }}</span>
                            </td>
                            <td class="text-xs text-gray-400">{{ $t->created_at->format('H:i') }}</td>
                            <td class="text-right">
                                <a href="{{ route('pos.cashier.receipt', $t) }}"
                                    class="text-[11px] font-semibold text-orange-500 hover:underline">
                                    Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-receipt text-2xl text-gray-200"></i>
                                    <p class="text-sm text-gray-400">Belum ada transaksi di shift ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
