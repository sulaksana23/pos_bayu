@extends ('layouts.app')
@section ('title','Akunting')

@section ('content')
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Akunting</h1>
                <p class="mt-0.5 text-sm text-gray-500">Ringkasan keuangan bulan {{ now()->translatedFormat('F Y') }}.</p>
            </div>
            <a
                href="{{ route('pos.accounting.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-bold text-white shadow hover:bg-orange-600"
            >
                <i class="fas fa-file-invoice"></i> Laporan Hari Ini
            </a>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-6">
            <div
                class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-4 text-white shadow-lg shadow-emerald-500/30"
            >
                <p class="text-[10px] tracking-wider uppercase opacity-80">Penjualan (MTD)</p>
                <p class="mt-1.5 text-xl font-bold">Rp {{ number_format($mtd['sales'],0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Trx</p>
                <p class="mt-1.5 text-2xl font-bold text-gray-900">{{ $mtd['count'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Rata-rata</p>
                <p class="mt-1.5 text-lg font-bold text-orange-600">Rp {{ number_format($mtd['avg'],0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Tunai</p>
                <p class="mt-1.5 text-xl font-bold text-emerald-600">Rp {{ number_format($mtd['cash'],0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Non-Tunai</p>
                <p class="mt-1.5 text-xl font-bold text-blue-600">Rp {{ number_format($mtd['noncash'],0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Pajak+Diskon</p>
                <p class="mt-1.5 text-sm font-bold text-gray-700">P: {{ number_format($mtd['tax'],0,',','.') }} / D: {{ number_format($mtd['discount'],0,',','.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-800">Trend Harian (Bulan Ini)</h3>
                    <span
                        class="rounded-full bg-orange-50 px-2 py-1 text-xs font-semibold text-orange-600"
                        >{{ now()->translatedFormat('F Y') }}</span
                    >
                </div>
                <div class="scroll-thin flex h-48 items-end gap-1 overflow-x-auto">
                    @foreach ($dailyTrend as $d)
                        <div class="flex h-full w-6 shrink-0 flex-col items-center justify-end">
                            <div class="font-mono text-[9px] text-gray-600">
                                {{ number_format($d['sales']/1000,0,'.','') }}k
                            </div>
                            <div
                                class="mt-0.5 w-full rounded-t bg-gradient-to-t from-orange-400 to-rose-500"
                                style="height: {{ min(100, max(2, ($d['sales'] / max(1, max(array_column($dailyTrend, 'sales')))) * 100)) }}%"
                            ></div>
                            <div class="mt-0.5 text-[9px] font-medium text-gray-500">
                                {{ $d['date'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-base font-bold text-gray-800">By Metode</h3>
                <div class="space-y-2">
                    @forelse ($byMethod as $m)
                        <div class="flex items-center gap-3">
                            <span
                                class="h-9 w-9 rounded-lg {{ $m->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-600' : ($m->payment_method === 'qris' ? 'bg-blue-100 text-blue-600' : 'bg-violet-100 text-violet-600') }} flex items-center justify-center"
                            >
                                <i
                                    class="fas {{ $m->payment_method === 'cash' ? 'fa-money-bill' : ($m->payment_method === 'qris' ? 'fa-qrcode' : 'fa-credit-card') }}"
                                ></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 uppercase">{{ $m->payment_method }}</p>
                                <p class="text-[11px] text-gray-500">{{ $m->count }} trx</p>
                            </div>
                            <p class="font-mono text-sm font-bold">Rp {{ number_format($m->total,0,',','.') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Belum ada data bulan ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 class="text-base font-bold text-gray-800">Shift Terbaru</h3>
                <a
                    href="{{ route('pos.shifts.index') }}"
                    class="text-xs font-semibold text-orange-600 hover:underline"
                    >Lihat semua</a
                >
            </div>
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50 text-xs text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Kasir</th>
                        <th class="px-4 py-3 text-right">Sales</th>
                        <th class="px-4 py-3 text-right">Trx</th>
                        <th class="px-4 py-3 text-right">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($recentShifts as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs">
                                {{ $s->opened_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3">{{ $s->user->name }}</td>
                            <td
                                class="px-4 py-3 text-right font-mono font-semibold text-emerald-600"
                            >
                                Rp {{ number_format($s->total_sales,0,',','.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                {{ $s->transaction_count }}
                            </td>
                            <td
                                class="px-4 py-3 text-right font-mono {{ (float)$s->cash_difference < 0 ? 'text-red-600' : ((float)$s->cash_difference > 0 ? 'text-emerald-600' : 'text-gray-500') }}"
                            >
                                @if ($s->cash_difference !== null) {{ ((float)$s->cash_difference > 0 ? '+' : '') }}Rp{{ number_format((float)$s->cash_difference,0,',','.') }}@else — @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">
                                Belum ada shift ditutup.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
