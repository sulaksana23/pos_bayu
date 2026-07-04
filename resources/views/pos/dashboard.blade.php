@extends('layouts.app', ['currentShift' => $openShift])
@section('title', 'Dashboard')

@section('content')
<div x-data="posDashboard()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                Selamat bertugas, {{ explode(' ', auth()->user()->name)[0] ?? 'Kak' }}!
            </h1>
            <p class="mt-0.5 text-sm text-gray-500">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ now()->translatedFormat('l, d F Y') }} &middot; <span x-text="clock"></span> WITA
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($openShift)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                    Shift Aktif sejak {{ $openShift->opened_at->format('H:i') }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500">
                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                    Tidak ada shift aktif
                </span>
            @endif
            <a href="{{ route('pos.cashier.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition-all hover:shadow-xl hover:shadow-orange-500/40">
                <i class="fas fa-cash-register"></i> Buka Kasir
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">

        {{-- Penjualan Hari Ini --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">Penjualan Hari Ini</p>
                    <p class="mt-1.5 truncate text-xl font-bold text-gray-900 sm:text-2xl">
                        Rp {{ number_format($todaySales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <i class="fas fa-coins text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <p class="text-xs text-gray-500"><i class="fas fa-receipt mr-1"></i>{{ $todayCount }} transaksi</p>
                @if($salesGrowth > 0)
                    <span class="text-xs font-semibold text-emerald-600"><i class="fas fa-arrow-up"></i> {{ $salesGrowth }}%</span>
                @elseif($salesGrowth < 0)
                    <span class="text-xs font-semibold text-red-500"><i class="fas fa-arrow-down"></i> {{ abs($salesGrowth) }}%</span>
                @else
                    <span class="text-xs font-semibold text-gray-400">— 0%</span>
                @endif
            </div>
            <p class="mt-1 text-[10px] text-gray-400">vs kemarin Rp {{ number_format($yesterdaySales, 0, ',', '.') }}</p>
        </div>

        {{-- Tunai --}}
        <div class="rounded-2xl bg-gradient-to-br from-orange-500 to-rose-500 p-4 text-white shadow-lg shadow-orange-500/30">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold tracking-wider text-white/80 uppercase">Tunai Hari Ini</p>
                    <p class="mt-1.5 truncate text-xl font-bold sm:text-2xl">Rp {{ number_format($todayCash, 0, ',', '.') }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20">
                    <i class="fas fa-money-bill-wave text-white"></i>
                </div>
            </div>
            <p class="mt-3 text-xs text-white/80"><i class="fas fa-credit-card mr-1"></i>Non-tunai Rp {{ number_format($todayNonCash, 0, ',', '.') }}</p>
        </div>

        {{-- Bulan Ini --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">Bulan Ini</p>
                    <p class="mt-1.5 truncate text-xl font-bold text-gray-900 sm:text-2xl">
                        Rp {{ number_format($monthSales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100">
                    <i class="fas fa-calendar-check text-blue-600"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <p class="text-xs text-gray-500">{{ $monthCount }} transaksi</p>
                @if($monthGrowth > 0)
                    <span class="text-xs font-semibold text-emerald-600"><i class="fas fa-arrow-up"></i> {{ $monthGrowth }}%</span>
                @elseif($monthGrowth < 0)
                    <span class="text-xs font-semibold text-red-500"><i class="fas fa-arrow-down"></i> {{ abs($monthGrowth) }}%</span>
                @else
                    <span class="text-xs font-semibold text-gray-400">— 0%</span>
                @endif
            </div>
            <p class="mt-1 text-[10px] text-gray-400">vs bln lalu Rp {{ number_format($lastMonthSales, 0, ',', '.') }}</p>
        </div>

        {{-- Rata-rata Transaksi --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">Rata-rata / Transaksi</p>
                    <p class="mt-1.5 truncate text-xl font-bold text-gray-900 sm:text-2xl">
                        Rp {{ number_format($avgTransaction, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-100">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-3">
                <p class="text-xs text-gray-500"><i class="fas fa-users mr-1"></i>{{ $totalCustomers }} pelanggan</p>
                @if($newCustomersToday > 0)
                    <span class="text-xs text-emerald-600">+{{ $newCustomersToday }} baru</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Middle Row: Chart + Payment Breakdown --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Weekly Chart --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Tren Penjualan 7 Hari</h2>
                    <p class="text-xs text-gray-400">Total: <span class="font-semibold text-gray-700">Rp {{ number_format(collect($weeklyTrend)->sum('sales'), 0, ',', '.') }}</span></p>
                </div>
                <div class="rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-semibold text-orange-600">
                    <i class="fas fa-chart-bar mr-1"></i> 7 Hari
                </div>
            </div>
            <div class="flex h-48 items-end gap-1.5 sm:gap-2">
                @php $maxSales = max(1, collect($weeklyTrend)->max('sales')); @endphp
                @foreach($weeklyTrend as $day)
                    @php
                        $height = $maxSales > 0 ? max(4, round(($day['sales'] / $maxSales) * 100)) : 4;
                        $isToday = $loop->last;
                    @endphp
                    <div class="group relative flex flex-1 flex-col items-center gap-1"
                         x-data="{ tooltip: false }">
                        {{-- Tooltip --}}
                        <div x-show="tooltip"
                             class="absolute bottom-full mb-2 z-10 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs text-white shadow-lg">
                            <p class="font-semibold">{{ $day['date'] }}</p>
                            <p>Rp {{ number_format($day['sales'], 0, ',', '.') }}</p>
                            <p class="text-gray-300">{{ $day['count'] }} transaksi</p>
                        </div>
                        <div class="w-full cursor-pointer rounded-t-lg transition-all duration-200 hover:opacity-80 {{ $isToday ? 'bg-gradient-to-t from-orange-500 to-rose-400' : 'bg-orange-200' }}"
                             style="height: {{ $height }}%"
                             @mouseenter="tooltip = true" @mouseleave="tooltip = false"
                             @touchstart="tooltip = !tooltip">
                        </div>
                        <span class="text-[10px] font-medium {{ $isToday ? 'text-orange-600' : 'text-gray-400' }}">
                            {{ $day['day'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Breakdown + Stock --}}
        <div class="flex flex-col gap-4">

            {{-- Payment Methods --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h2 class="mb-3 font-bold text-gray-900">Metode Pembayaran</h2>
                @php
                    $methods = [
                        'cash'     => ['label' => 'Tunai',    'icon' => 'fa-money-bill-wave', 'color' => 'bg-emerald-500'],
                        'qris'     => ['label' => 'QRIS',     'icon' => 'fa-qrcode',          'color' => 'bg-blue-500'],
                        'transfer' => ['label' => 'Transfer', 'icon' => 'fa-university',      'color' => 'bg-purple-500'],
                        'wallet'   => ['label' => 'E-Wallet', 'icon' => 'fa-wallet',          'color' => 'bg-pink-500'],
                    ];
                @endphp
                @forelse($methods as $key => $m)
                    @php $pm = $paymentBreakdown[$key] ?? null; @endphp
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $m['color'] }} bg-opacity-10">
                            <i class="fas {{ $m['icon'] }} text-xs" style="color: inherit"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-700">{{ $m['label'] }}</p>
                            <p class="text-[10px] text-gray-400">{{ $pm ? $pm->count : 0 }} transaksi</p>
                        </div>
                        <p class="text-xs font-bold text-gray-900">Rp {{ number_format($pm ? $pm->total : 0, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">Belum ada transaksi hari ini</p>
                @endforelse
            </div>

            {{-- Stock Alert --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h2 class="mb-3 font-bold text-gray-900">Status Stok</h2>
                <div class="space-y-2">
                    <div class="flex items-center justify-between rounded-xl bg-gray-50 px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                            <span class="text-xs text-gray-600">Total Produk Aktif</span>
                        </div>
                        <span class="text-xs font-bold text-gray-900">{{ $totalProducts }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-50' : 'bg-gray-50' }} px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-500' : 'bg-gray-300' }}"></div>
                            <span class="text-xs {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-600' }}">Stok Menipis</span>
                        </div>
                        <span class="text-xs font-bold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-900' }}">{{ $lowStockCount }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl {{ $outOfStock > 0 ? 'bg-red-50' : 'bg-gray-50' }} px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full {{ $outOfStock > 0 ? 'bg-red-500' : 'bg-gray-300' }}"></div>
                            <span class="text-xs {{ $outOfStock > 0 ? 'text-red-600' : 'text-gray-600' }}">Stok Habis</span>
                        </div>
                        <span class="text-xs font-bold {{ $outOfStock > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $outOfStock }}</span>
                    </div>
                </div>
                @if($lowStockCount > 0 || $outOfStock > 0)
                    <a href="{{ route('pos.inventory.index') }}"
                       class="mt-3 block rounded-xl border border-amber-200 bg-amber-50 py-2 text-center text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                        <i class="fas fa-boxes mr-1"></i> Kelola Inventori
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Row: Top Products + Recent Transactions --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

        {{-- Top Products --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Produk Terlaris <span class="text-xs font-normal text-gray-400">(7 hari)</span></h2>
                <a href="{{ route('pos.reports.products') }}" class="text-xs text-orange-500 hover:underline">Lihat semua</a>
            </div>
            @if($topProducts->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <i class="fas fa-box-open text-3xl text-gray-200 mb-2"></i>
                    <p class="text-sm text-gray-400">Belum ada data penjualan</p>
                </div>
            @else
                @php $maxRevenue = max(1, $topProducts->max('revenue')); @endphp
                <div class="space-y-3">
                    @foreach($topProducts as $i => $p)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[10px] font-bold text-orange-600">{{ $i + 1 }}</span>
                                    <span class="truncate text-sm font-medium text-gray-800">{{ $p->product_name }}</span>
                                </div>
                                <div class="flex shrink-0 items-center gap-3 text-xs text-gray-500">
                                    <span>{{ number_format($p->qty, 0, ',', '.') }} pcs</span>
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($p->revenue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-gray-100">
                                <div class="h-1.5 rounded-full bg-gradient-to-r from-orange-400 to-rose-400"
                                     style="width: {{ round(($p->revenue / $maxRevenue) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Transactions --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Transaksi Terbaru</h2>
                <a href="{{ route('pos.reports.sales') }}" class="text-xs text-orange-500 hover:underline">Lihat semua</a>
            </div>
            @if($recent->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <i class="fas fa-receipt text-3xl text-gray-200 mb-2"></i>
                    <p class="text-sm text-gray-400">Belum ada transaksi hari ini</p>
                </div>
            @else
                <div class="space-y-2 max-h-80 overflow-y-auto scroll-thin pr-1">
                    @foreach($recent as $tx)
                        <div class="flex items-center gap-3 rounded-xl border border-gray-50 p-3 transition hover:bg-gray-50">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                                @if($tx->payment_method === 'cash') bg-emerald-100
                                @elseif($tx->payment_method === 'qris') bg-blue-100
                                @elseif($tx->payment_method === 'transfer') bg-purple-100
                                @else bg-pink-100 @endif">
                                <i class="fas text-sm
                                    @if($tx->payment_method === 'cash') fa-money-bill-wave text-emerald-600
                                    @elseif($tx->payment_method === 'qris') fa-qrcode text-blue-600
                                    @elseif($tx->payment_method === 'transfer') fa-university text-purple-600
                                    @else fa-wallet text-pink-600 @endif"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $tx->invoice_no }}</p>
                                <p class="text-[11px] text-gray-400">
                                    {{ $tx->customer?->name ?? 'Umum' }} &middot; {{ $tx->items->count() }} item &middot; {{ $tx->created_at->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($tx->total, 0, ',', '.') }}</p>
                                <a href="{{ route('pos.cashier.receipt', $tx) }}"
                                   class="text-[11px] text-orange-500 hover:underline">Struk</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function posDashboard() {
        return {
            clock: '{{ now()->format('H:i') }}',
            init() {
                setInterval(() => {
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    this.clock = h + ':' + m;
                }, 10000);
            },
        };
    }
</script>
@endpush
