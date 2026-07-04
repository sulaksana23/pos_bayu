@extends('layouts.app', ['currentShift' => $openShift])
@section('title', 'Dashboard')

@section('content')
<div x-data="posDashboard()" class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">
                Selamat bertugas, {{ explode(' ', auth()->user()->name)[0] ?? 'Kak' }}!
            </h1>
            <p class="text-xs text-gray-400">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ now()->translatedFormat('l, d F Y') }} &middot; <span x-text="clock"></span> WITA
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($openShift)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                    Shift sejak {{ $openShift->opened_at->format('H:i') }}
                </span>
            @else
                <a href="{{ route('pos.shifts.open') }}"
                   class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-200 transition">
                    <i class="fas fa-clock"></i> Buka Shift
                </a>
            @endif
            <a href="{{ route('pos.cashier.index') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
                <i class="fas fa-cash-register"></i> Kasir
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

        {{-- Penjualan Hari Ini --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-3.5 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Hari Ini</p>
                    <p class="mt-1 truncate text-lg font-bold text-gray-900">
                        Rp {{ number_format($todaySales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <i class="fas fa-coins text-sm text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <p class="text-[11px] text-gray-400">{{ $todayCount }} transaksi</p>
                @if($salesGrowth > 0)
                    <span class="text-[11px] font-bold text-emerald-600"><i class="fas fa-arrow-up text-[9px]"></i> {{ $salesGrowth }}%</span>
                @elseif($salesGrowth < 0)
                    <span class="text-[11px] font-bold text-red-500"><i class="fas fa-arrow-down text-[9px]"></i> {{ abs($salesGrowth) }}%</span>
                @else
                    <span class="text-[11px] font-bold text-gray-300">0%</span>
                @endif
            </div>
        </div>

        {{-- Tunai --}}
        <div class="rounded-2xl bg-gradient-to-br from-orange-500 to-rose-500 p-3.5 text-white shadow-md shadow-orange-500/25">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-white/70 uppercase">Tunai</p>
                    <p class="mt-1 truncate text-lg font-bold">Rp {{ number_format($todayCash, 0, ',', '.') }}</p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
            </div>
            <p class="mt-2 text-[11px] text-white/70">Non-tunai Rp {{ number_format($todayNonCash, 0, ',', '.') }}</p>
        </div>

        {{-- Bulan Ini --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-3.5 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Bulan Ini</p>
                    <p class="mt-1 truncate text-lg font-bold text-gray-900">
                        Rp {{ number_format($monthSales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100">
                    <i class="fas fa-calendar-check text-sm text-blue-600"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <p class="text-[11px] text-gray-400">{{ $monthCount }} transaksi</p>
                @if($monthGrowth > 0)
                    <span class="text-[11px] font-bold text-emerald-600"><i class="fas fa-arrow-up text-[9px]"></i> {{ $monthGrowth }}%</span>
                @elseif($monthGrowth < 0)
                    <span class="text-[11px] font-bold text-red-500"><i class="fas fa-arrow-down text-[9px]"></i> {{ abs($monthGrowth) }}%</span>
                @else
                    <span class="text-[11px] font-bold text-gray-300">0%</span>
                @endif
            </div>
        </div>

        {{-- Shift / Rata-rata --}}
        @if($openShift)
        <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 p-3.5 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-emerald-600 uppercase">Shift Ini</p>
                    <p class="mt-1 truncate text-lg font-bold text-gray-900">
                        Rp {{ number_format($shiftSales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <i class="fas fa-business-time text-sm text-emerald-600"></i>
                </div>
            </div>
            <p class="mt-2 text-[11px] text-gray-400">{{ $shiftCount }} transaksi &middot; {{ $activeCashiers }} kasir aktif</p>
        </div>
        @else
        <div class="rounded-2xl border border-gray-100 bg-white p-3.5 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Rata-rata</p>
                    <p class="mt-1 truncate text-lg font-bold text-gray-900">
                        Rp {{ number_format($avgTransaction, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-purple-100">
                    <i class="fas fa-chart-line text-sm text-purple-600"></i>
                </div>
            </div>
            <p class="mt-2 text-[11px] text-gray-400">{{ $totalCustomers }} pelanggan
                @if($newCustomersToday > 0)<span class="text-emerald-600"> +{{ $newCustomersToday }} baru</span>@endif
            </p>
        </div>
        @endif
    </div>

    {{-- Middle Row: Chart + Right Column --}}
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">

        {{-- Weekly Chart --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Tren 7 Hari</h2>
                    <p class="text-[11px] text-gray-400">Total Rp {{ number_format(collect($weeklyTrend)->sum('sales'), 0, ',', '.') }}</p>
                </div>
                <span class="rounded-lg bg-orange-50 px-2.5 py-1 text-[11px] font-semibold text-orange-600">
                    <i class="fas fa-chart-bar mr-1"></i> 7 Hari
                </span>
            </div>
            <div class="flex h-40 items-end gap-1 sm:gap-1.5">
                @php $maxSales = max(1, collect($weeklyTrend)->max('sales')); @endphp
                @foreach($weeklyTrend as $day)
                    @php
                        $height = $maxSales > 0 ? max(5, round(($day['sales'] / $maxSales) * 100)) : 5;
                        $isToday = $loop->last;
                    @endphp
                    <div class="group relative flex flex-1 flex-col items-center gap-1" x-data="{ tt: false }">
                        <div x-show="tt" x-cloak
                             class="absolute bottom-full mb-2 z-10 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs text-white shadow-xl pointer-events-none">
                            <p class="font-semibold">{{ $day['date'] }}</p>
                            <p>Rp {{ number_format($day['sales'], 0, ',', '.') }}</p>
                            <p class="text-gray-300">{{ $day['count'] }} transaksi</p>
                        </div>
                        <div class="w-full cursor-pointer rounded-t-md transition-all duration-200 hover:opacity-75 {{ $isToday ? 'bg-gradient-to-t from-orange-500 to-rose-400 shadow-sm shadow-orange-500/40' : 'bg-orange-100 hover:bg-orange-200' }}"
                             style="height: {{ $height }}%"
                             @mouseenter="tt = true" @mouseleave="tt = false"
                             @touchstart.prevent="tt = !tt">
                        </div>
                        <span class="text-[9px] font-semibold {{ $isToday ? 'text-orange-600' : 'text-gray-400' }}">
                            {{ $day['day'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Column --}}
        <div class="flex flex-col gap-3">

            {{-- Payment Methods --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <h2 class="mb-2.5 text-sm font-bold text-gray-900">Pembayaran Hari Ini</h2>
                @php
                    $methods = [
                        'cash'     => ['label' => 'Tunai',    'icon' => 'fa-money-bill-wave', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
                        'qris'     => ['label' => 'QRIS',     'icon' => 'fa-qrcode',          'bg' => 'bg-blue-100',    'text' => 'text-blue-600'],
                        'transfer' => ['label' => 'Transfer', 'icon' => 'fa-university',      'bg' => 'bg-purple-100',  'text' => 'text-purple-600'],
                        'wallet'   => ['label' => 'E-Wallet', 'icon' => 'fa-wallet',          'bg' => 'bg-pink-100',    'text' => 'text-pink-600'],
                    ];
                @endphp
                <div class="space-y-1.5">
                    @foreach($methods as $key => $m)
                        @php $pm = $paymentBreakdown[$key] ?? null; @endphp
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $m['bg'] }}">
                                <i class="fas {{ $m['icon'] }} text-[11px] {{ $m['text'] }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-gray-700">{{ $m['label'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-900">Rp {{ number_format($pm ? $pm->total : 0, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $pm ? $pm->count : 0 }}x</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Stock Status --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="mb-2.5 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-900">Status Stok</h2>
                    @if($lowStockCount > 0 || $outOfStock > 0)
                        <a href="{{ route('pos.inventory.index') }}" class="text-[11px] font-semibold text-orange-500 hover:underline">Kelola</a>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-1.5">
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                            <span class="text-xs text-gray-600">Produk Aktif</span>
                        </div>
                        <span class="text-xs font-bold text-gray-900">{{ $totalProducts }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg {{ $lowStockCount > 0 ? 'bg-amber-50' : 'bg-gray-50' }} px-3 py-1.5">
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-1.5 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-500' : 'bg-gray-300' }}"></div>
                            <span class="text-xs {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-600' }}">Stok Menipis</span>
                        </div>
                        <span class="text-xs font-bold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-400' }}">{{ $lowStockCount }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg {{ $outOfStock > 0 ? 'bg-red-50' : 'bg-gray-50' }} px-3 py-1.5">
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-1.5 rounded-full {{ $outOfStock > 0 ? 'bg-red-500' : 'bg-gray-300' }}"></div>
                            <span class="text-xs {{ $outOfStock > 0 ? 'text-red-600' : 'text-gray-600' }}">Stok Habis</span>
                        </div>
                        <span class="text-xs font-bold {{ $outOfStock > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $outOfStock }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row: Top Products + Recent Transactions --}}
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">

        {{-- Top Products --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Produk Terlaris <span class="text-[11px] font-normal text-gray-400">7 hari</span></h2>
                <a href="{{ route('pos.reports.products') }}" class="text-[11px] font-semibold text-orange-500 hover:underline">Semua</a>
            </div>
            @if($topProducts->isEmpty())
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <i class="fas fa-box-open text-2xl text-gray-200 mb-1.5"></i>
                    <p class="text-xs text-gray-400">Belum ada data penjualan</p>
                </div>
            @else
                @php $maxRevenue = max(1, $topProducts->max('revenue')); @endphp
                <div class="space-y-2.5">
                    @foreach($topProducts as $i => $p)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[9px] font-bold text-orange-600">{{ $i + 1 }}</span>
                                    <span class="truncate text-xs font-medium text-gray-800">{{ $p->product_name }}</span>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 text-[11px] text-gray-400">
                                    <span>{{ number_format($p->qty, 0) }} pcs</span>
                                    <span class="font-bold text-gray-700">Rp {{ number_format($p->revenue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="h-1 w-full rounded-full bg-gray-100">
                                <div class="h-1 rounded-full bg-gradient-to-r from-orange-400 to-rose-400"
                                     style="width: {{ round(($p->revenue / $maxRevenue) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Transactions --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Transaksi Terbaru</h2>
                <a href="{{ route('pos.reports.sales') }}" class="text-[11px] font-semibold text-orange-500 hover:underline">Semua</a>
            </div>
            @if($recent->isEmpty())
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <i class="fas fa-receipt text-2xl text-gray-200 mb-1.5"></i>
                    <p class="text-xs text-gray-400">Belum ada transaksi</p>
                </div>
            @else
                <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1 scroll-thin">
                    @foreach($recent as $tx)
                        @php
                            $pmIcon = match($tx->payment_method) {
                                'cash'     => ['bg' => 'bg-emerald-100', 'icon' => 'fa-money-bill-wave', 'text' => 'text-emerald-600'],
                                'qris'     => ['bg' => 'bg-blue-100',    'icon' => 'fa-qrcode',          'text' => 'text-blue-600'],
                                'transfer' => ['bg' => 'bg-purple-100',  'icon' => 'fa-university',      'text' => 'text-purple-600'],
                                default    => ['bg' => 'bg-pink-100',    'icon' => 'fa-wallet',          'text' => 'text-pink-600'],
                            };
                        @endphp
                        <div class="flex items-center gap-2.5 rounded-xl p-2.5 transition hover:bg-gray-50">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $pmIcon['bg'] }}">
                                <i class="fas {{ $pmIcon['icon'] }} text-xs {{ $pmIcon['text'] }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-gray-900">{{ $tx->invoice_no }}</p>
                                <p class="text-[10px] text-gray-400">
                                    {{ $tx->customer?->name ?? 'Umum' }} &middot; {{ $tx->items->count() }} item &middot; {{ $tx->created_at->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs font-bold text-gray-900">Rp {{ number_format($tx->total, 0, ',', '.') }}</p>
                                <a href="{{ route('pos.cashier.receipt', $tx) }}"
                                   class="text-[10px] text-orange-500 hover:underline">Struk</a>
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
            clock: '{{ now()->format("H:i") }}',
            init() {
                setInterval(() => {
                    const now = new Date();
                    this.clock = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
                }, 10000);
            },
        };
    }
</script>
@endpush
