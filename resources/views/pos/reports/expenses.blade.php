@extends('layouts.app')
@section('title', 'Laporan Biaya')
@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Laporan Biaya</h1>
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
                <option value="">Semua</option>
                @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-semibold text-white hover:bg-orange-500 transition">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Biaya</p>
            <p class="mt-1 text-xl font-bold text-red-600">Rp {{ number_format($summary['total_expenses'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-orange-100 bg-orange-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-orange-600">Jumlah Transaksi</p>
            <p class="mt-1 text-xl font-bold text-orange-700">{{ number_format($summary['total_count'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-purple-100 bg-purple-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600">Rata-rata / Biaya</p>
            <p class="mt-1 text-xl font-bold text-purple-700">Rp {{ number_format($summary['avg_expense'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Breakdown by Category --}}
    @if($breakdown->count())
    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <h3 class="mb-3 text-sm font-bold text-gray-900">Biaya per Kategori</h3>
        <div class="space-y-2">
            @foreach($breakdown as $b)
            @php
                $pct = $summary['total_expenses'] > 0 ? round(($b->total / $summary['total_expenses']) * 100, 1) : 0;
                $badgeClass = match($b->category) {
                    'operational' => 'bg-blue-100 text-blue-700',
                    'utilities' => 'bg-yellow-100 text-yellow-700',
                    'rent' => 'bg-purple-100 text-purple-700',
                    'salary' => 'bg-green-100 text-green-700',
                    'maintenance' => 'bg-orange-100 text-orange-700',
                    'marketing' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-700',
                };
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-medium {{ $badgeClass }}">
                            {{ $categories[$b->category] ?? $b->category }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $b->count }}x</span>
                    </div>
                    <span class="text-xs font-bold text-gray-700">Rp {{ number_format($b->total, 0, ',', '.') }} ({{ $pct }}%)</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-gray-100">
                    <div class="h-1.5 rounded-full bg-gradient-to-r from-orange-400 to-rose-500 transition-all"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabel Biaya --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Riwayat Biaya</h3>
            <span class="text-xs text-gray-400">{{ $expenses->total() }} biaya</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="border-b border-gray-100 bg-gray-50 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5 text-left">No. Biaya</th>
                        <th class="px-4 py-2.5 text-left">Tanggal</th>
                        <th class="px-4 py-2.5 text-left">Kategori</th>
                        <th class="px-4 py-2.5 text-left">Deskripsi</th>
                        <th class="px-4 py-2.5 text-right">Jumlah</th>
                        <th class="px-4 py-2.5 text-center">Pembayaran</th>
                        <th class="px-4 py-2.5 text-left">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $exp)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2.5 font-mono text-gray-600">{{ $exp->expense_no }}</td>
                        <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">{{ $exp->expense_date->isoFormat('D MMM YYYY') }}</td>
                        <td class="px-4 py-2.5">
                            @php
                                $badgeClass = match($exp->category) {
                                    'operational' => 'bg-blue-100 text-blue-700',
                                    'utilities' => 'bg-yellow-100 text-yellow-700',
                                    'rent' => 'bg-purple-100 text-purple-700',
                                    'salary' => 'bg-green-100 text-green-700',
                                    'maintenance' => 'bg-orange-100 text-orange-700',
                                    'marketing' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium {{ $badgeClass }}">
                                {{ $categories[$exp->category] ?? $exp->category }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-700 max-w-[200px] truncate">{{ $exp->description }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-red-600">-Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="text-gray-600">{{ $exp->payment_method ? ucfirst($exp->payment_method) : '-' }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $exp->user?->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Tidak ada biaya pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $expenses->links() }}</div>
    </div>
</div>
@endsection
