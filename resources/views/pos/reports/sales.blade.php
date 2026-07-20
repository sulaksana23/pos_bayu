@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
<div class="space-y-4" x-data="salesReport()">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Laporan Penjualan</h1>
            <p class="mt-0.5 text-xs text-gray-400">{{ $startDate }} s/d {{ $endDate }}</p>
        </div>
        <a href="{{ route('pos.reports.sales', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="inline-flex items-center gap-1.5 rounded-xl border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-100 transition">
            <i class="fas fa-download"></i> Export CSV
        </a>
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
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">Kelompokkan</label>
            <select name="group_by" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:border-orange-500 focus:outline-none">
                <option value="day" {{ $groupBy === 'day' ? 'selected' : '' }}>Per Hari</option>
                <option value="week" {{ $groupBy === 'week' ? 'selected' : '' }}>Per Minggu</option>
                <option value="month" {{ $groupBy === 'month' ? 'selected' : '' }}>Per Bulan</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-semibold text-white hover:bg-orange-500 transition">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Pendapatan</p>
            <p class="mt-1 text-xl font-bold text-gray-900">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Total Transaksi</p>
            <p class="mt-1 text-xl font-bold text-blue-700">{{ number_format($summary['total_transactions'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-purple-100 bg-purple-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600">Rata-rata / Transaksi</p>
            <p class="mt-1 text-xl font-bold text-purple-700">Rp {{ number_format($summary['avg_transaction'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Item Terjual</p>
            <p class="mt-1 text-xl font-bold text-emerald-700">{{ number_format($summary['total_items_sold'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <h3 class="mb-3 text-sm font-bold text-gray-900">Grafik Penjualan</h3>
        <div id="salesChart" style="min-height:220px"></div>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Riwayat Transaksi</h3>
            <span class="text-xs text-gray-400">{{ $transactions->total() }} transaksi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="border-b border-gray-100 bg-gray-50 text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Invoice</th>
                        <th class="px-4 py-2.5 text-left">Waktu</th>
                        <th class="px-4 py-2.5 text-left">Pelanggan</th>
                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                        <th class="px-4 py-2.5 text-right">Diskon</th>
                        <th class="px-4 py-2.5 text-right">Total</th>
                        <th class="px-4 py-2.5 text-center">Pembayaran</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2.5 font-mono text-gray-600">{{ $t->invoice_no }}</td>
                        <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">{{ $t->created_at->isoFormat('D MMM, HH:mm') }}</td>
                        <td class="px-4 py-2.5 text-gray-700">{{ $t->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-600">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right text-orange-600">
                            @if($t->discount > 0) -Rp {{ number_format($t->discount, 0, ',', '.') }} @else - @endif
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-900">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700">{{ strtoupper($t->payment_method) }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if($t->status === 'completed')
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Selesai</span>
                            @else
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-600">{{ $t->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">Tidak ada transaksi pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $transactions->links() }}</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function salesReport() { return {}; }
const chartData = @json($chartData);
document.addEventListener('DOMContentLoaded', () => {
    if (!chartData.length) return;
    new ApexCharts(document.querySelector('#salesChart'), {
        series: [
            { name: 'Pendapatan', type: 'area', data: chartData.map(d => parseFloat(d.revenue)) },
            { name: 'Transaksi', type: 'bar', data: chartData.map(d => parseInt(d.transaction_count)) },
        ],
        chart: { height: 220, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        colors: ['#f97316', '#3b82f6'],
        fill: { type: ['gradient', 'solid'], gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: [2, 0] },
        xaxis: { categories: chartData.map(d => d.period), labels: { style: { fontSize: '10px' } } },
        yaxis: [
            { labels: { formatter: v => 'Rp ' + (v >= 1e6 ? (v/1e6).toFixed(1)+'jt' : (v/1e3).toFixed(0)+'rb'), style: { fontSize: '10px' } } },
            { opposite: true, labels: { formatter: v => v+'x', style: { fontSize: '10px' } } },
        ],
        tooltip: { y: [{ formatter: v => 'Rp '+new Intl.NumberFormat('id-ID').format(v) }, { formatter: v => v+' transaksi' }] },
        legend: { position: 'top', fontSize: '11px' },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    }).render();
});
</script>
@endsection
