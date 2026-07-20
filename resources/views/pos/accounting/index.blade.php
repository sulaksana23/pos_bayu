@extends('layouts.app')
@section('title', 'Akunting')
@section('breadcrumb', 'Akunting')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Akunting</h1>
            <p class="text-xs text-gray-400">Ringkasan keuangan bulan {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <a href="{{ route('pos.accounting.daily') }}"
            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-600 transition-colors">
            <i class="fas fa-file-invoice"></i> Laporan Hari Ini
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Penjualan (MTD)</p>
            <p class="mt-1.5 font-mono text-base font-bold text-emerald-700">Rp {{ number_format($mtd['sales'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Transaksi</p>
            <p class="mt-1.5 font-mono text-base font-bold text-gray-900">{{ $mtd['count'] }}</p>
        </div>
        <div class="rounded-xl border border-orange-200 bg-orange-50 p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-orange-600">Rata-rata</p>
            <p class="mt-1.5 font-mono text-base font-bold text-orange-700">Rp {{ number_format($mtd['avg'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Tunai</p>
            <p class="mt-1.5 font-mono text-base font-bold text-emerald-600">Rp {{ number_format($mtd['cash'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Non-Tunai</p>
            <p class="mt-1.5 font-mono text-base font-bold text-blue-600">Rp {{ number_format($mtd['noncash'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pajak+Diskon</p>
            <p class="mt-1.5 font-mono text-base font-bold text-gray-700">Rp {{ number_format(($mtd['tax'] ?? 0) - ($mtd['discount'] ?? 0), 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Daily Sales Chart --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-800">Penjualan Harian ({{ now()->translatedFormat('F Y') }})</h3>
        </div>
        <div class="p-4">
            <canvas id="dailyChart" height="80"></canvas>
        </div>
    </div>

    {{-- Shift Summary --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-bold text-gray-800">Rekap Shift Bulan Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th class="text-right">Opening</th>
                        <th class="text-right">Sales</th>
                        <th class="text-right">Trx</th>
                        <th class="text-right">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentShifts as $s)
                        <tr>
                            <td class="text-xs text-gray-700">{{ $s->opened_at->translatedFormat('d M') }}</td>
                            <td class="text-xs text-gray-700">{{ $s->user->name }}</td>
                            <td class="text-right font-mono text-xs">Rp {{ number_format($s->opening_cash, 0, ',', '.') }}</td>
                            <td class="text-right font-mono text-xs font-semibold text-emerald-600">Rp {{ number_format($s->total_sales, 0, ',', '.') }}</td>
                            <td class="text-right font-mono text-xs">{{ $s->transaction_count }}</td>
                            <td class="text-right font-mono text-xs {{ (float)$s->cash_difference < 0 ? 'font-semibold text-red-600' : ((float)$s->cash_difference > 0 ? 'font-semibold text-emerald-600' : 'text-gray-400') }}">
                                @if ($s->cash_difference !== null)
                                    {{ (float)$s->cash_difference > 0 ? '+' : '' }}Rp {{ number_format((float)$s->cash_difference, 0, ',', '.') }}
                                @else —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm text-gray-400">Belum ada shift ditutup bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('dailyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($dailyData ?? [])->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d'))->values()) !!},
                datasets: [{
                    label: 'Penjualan',
                    data: {!! json_encode(collect($dailyData ?? [])->pluck('total')->values()) !!},
                    backgroundColor: 'rgba(249,115,22,0.15)',
                    borderColor: 'rgb(249,115,22)',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: v => 'Rp ' + Intl.NumberFormat('id').format(v),
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }
</script>
@endpush
