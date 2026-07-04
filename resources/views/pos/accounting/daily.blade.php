@extends ('layouts.app')
@section ('title','Laporan Harian')

@section ('content')
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Harian</h1>
                <p class="text-sm text-gray-500">Detail transaksi tanggal {{ $date->translatedFormat('l, d F Y') }}.</p>
            </div>
            <form method="GET" class="inline-flex items-center gap-2">
                <input
                    type="date"
                    name="date"
                    value="{{ $date->format('Y-m-d') }}"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm"
                />
                <button class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white">
                    Lihat
                </button>
            </form>
        </div>

        @php
        $totalSales = $transactions->sum('total');
        $totalCash = $transactions->where('payment_method','cash')->sum('paid');
        $totalNonCash = $transactions->where('payment_method','!=','cash')->sum('paid');
        $byMethod = $transactions->groupBy('payment_method')->map(fn ($g) => ['count' => $g->count(), 'total' => $g->sum('total')]);
    @endphp

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div
                class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-4 text-white shadow-lg"
            >
                <p class="text-[10px] uppercase opacity-80">Total Penjualan</p>
                <p class="mt-1.5 text-xl font-bold">Rp {{ number_format($totalSales,0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Trx</p>
                <p class="mt-1.5 text-2xl font-bold">{{ $transactions->count() }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Tunai</p>
                <p class="mt-1.5 text-xl font-bold text-emerald-600">Rp {{ number_format($totalCash,0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Non-Tunai</p>
                <p class="mt-1.5 text-xl font-bold text-blue-600">Rp {{ number_format($totalNonCash,0,',','.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50 text-xs text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-right">Item</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Bayar</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($transactions as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">
                                <a
                                    href="{{ route('pos.cashier.receipt', $t) }}"
                                    class="hover:text-orange-600"
                                    >{{ $t->invoice_no }}</a
                                >
                            </td>
                            <td class="px-4 py-3">{{ $t->customer?->name ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-right text-xs text-gray-500">
                                {{ $t->items->count() }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold">
                                Rp {{ number_format($t->total,0,',','.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                Rp {{ number_format($t->paid,0,',','.') }}
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">
                                {{ $t->payment_method }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $t->created_at->format('H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                                Tidak ada transaksi pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
