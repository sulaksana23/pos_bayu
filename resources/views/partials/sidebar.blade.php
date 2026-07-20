@php
    $isAdmin   = auth()->user()?->isAdmin();
    $isManager = auth()->user()?->isManager();
@endphp

{{-- Mobile backdrop --}}
<div
    x-show="$store.sidebar.open"
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.sidebar.close()"
    class="fixed inset-0 z-20 bg-black/50 lg:hidden"
    x-cloak
    aria-hidden="true"
></div>

{{-- Sidebar --}}
<aside
    id="appSidebar"
    :class="{
        '-translate-x-full': !$store.sidebar.open,
        'translate-x-0':      $store.sidebar.open,
        'w-[260px]':          $store.sidebar.open || !$store.sidebar.collapsed,
        'w-[68px]':           !$store.sidebar.open && $store.sidebar.collapsed,
    }"
    class="fixed top-0 bottom-0 left-0 z-30
           flex flex-col overflow-y-auto overflow-x-hidden
           bg-white border-r border-gray-200
           transition-all duration-200 ease-in-out
           lg:translate-x-0"
    aria-label="Sidebar navigasi"
>
    {{-- Logo --}}
    <div class="flex h-[60px] shrink-0 items-center justify-between border-b border-gray-200 px-3.5">

        {{-- Logo expanded --}}
        <a
            href="{{ route('pos.dashboard') }}"
            :class="{ 'flex': $store.sidebar.open || !$store.sidebar.collapsed, 'hidden': !$store.sidebar.open && $store.sidebar.collapsed }"
            class="items-center gap-2.5 min-w-0"
        >
            {{-- Hexagon BT icon --}}
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                <defs>
                    <linearGradient id="posGrad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#fb923c"/>
                        <stop offset="100%" stop-color="#ea580c"/>
                    </linearGradient>
                </defs>
                <path d="M16 2L28.7 9.5V24.5L16 32L3.3 24.5V9.5L16 2Z" fill="url(#posGrad)"/>
                <text x="16" y="21" text-anchor="middle" font-family="'Inter', ui-sans-serif, sans-serif" font-weight="800" font-size="11" letter-spacing="-0.5" fill="white">BT</text>
            </svg>
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-900 leading-tight">Bali<span class="text-orange-500">POS</span></p>
                <p class="text-[10px] text-gray-400 leading-tight tracking-wide">v{{ config('app.version', '1.0.0') }}</p>
            </div>
        </a>

        {{-- Logo collapsed --}}
        <a
            href="{{ route('pos.dashboard') }}"
            :class="{ 'flex': !$store.sidebar.open && $store.sidebar.collapsed, 'hidden': $store.sidebar.open || !$store.sidebar.collapsed }"
            class="mx-auto items-center justify-center"
        >
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="posGrad2" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#fb923c"/>
                        <stop offset="100%" stop-color="#ea580c"/>
                    </linearGradient>
                </defs>
                <path d="M16 2L28.7 9.5V24.5L16 32L3.3 24.5V9.5L16 2Z" fill="url(#posGrad2)"/>
                <text x="16" y="21" text-anchor="middle" font-family="'Inter', ui-sans-serif, sans-serif" font-weight="800" font-size="11" letter-spacing="-0.5" fill="white">BT</text>
            </svg>
        </a>

        {{-- Collapse toggle (desktop only) --}}
        <button
            @click="$store.sidebar.toggleCollapse()"
            :class="{ 'hidden': !$store.sidebar.open && $store.sidebar.collapsed }"
            class="hidden lg:flex items-center justify-center h-7 w-7 shrink-0 rounded-md
                   text-gray-400 hover:bg-gray-100 hover:text-gray-600
                   transition-colors duration-150"
            aria-label="Toggle sidebar"
        >
            <svg
                :class="{ 'rotate-180': $store.sidebar.collapsed }"
                class="h-3.5 w-3.5 transition-transform duration-200"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 py-2 overflow-y-auto overflow-x-hidden" aria-label="Menu utama">

        {{-- ── OPERASIONAL ── --}}
        <div x-show="$store.sidebar.showLabels" class="nav-section">Operasional</div>

        @php
        $operasional = [
            ['route' => 'pos.dashboard',     'match' => 'pos.dashboard',  'label' => 'Dashboard',
             'svg' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'],
            ['route' => 'pos.cashier.index', 'match' => 'pos.cashier.*', 'label' => 'Kasir',
             'svg' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M6 8h.01M10 8h4"/>'],
            ['route' => 'pos.shifts.index',  'match' => 'pos.shifts.*',  'label' => 'Shift',
             'svg' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
        ];
        @endphp
        @foreach ($operasional as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-item {{ request()->routeIs($item['match']) ? 'active' : '' }}"
           :class="{ 'justify-center !mx-0 !w-full !rounded-none px-0': !$store.sidebar.open && $store.sidebar.collapsed }">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $item['svg'] !!}</svg>
            <span x-show="$store.sidebar.showLabels" class="nav-label">{{ $item['label'] }}</span>
        </a>
        @endforeach

        {{-- ── KATALOG ── --}}
        <div x-show="$store.sidebar.showLabels" class="nav-section mt-1">Katalog</div>

        @php
        $katalog = [
            ['route' => 'pos.products.index',   'match' => 'pos.products.*',   'label' => 'Produk',
             'svg' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
            ['route' => 'pos.categories.index', 'match' => 'pos.categories.*', 'label' => 'Kategori',
             'svg' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'],
            ['route' => 'pos.inventory.index',  'match' => 'pos.inventory.*',  'label' => 'Stok & Gudang',
             'svg' => '<path d="M5 8h14M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm14 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>'],
            ['route' => 'pos.customers.index',  'match' => 'pos.customers.*',  'label' => 'Pelanggan',
             'svg' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
            ['route' => 'pos.suppliers.index',  'match' => 'pos.suppliers.*',  'label' => 'Supplier',
             'svg' => '<path d="M5 8h14M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm14 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/><path d="M9 12h3l2-2M9 16h5"/>'],
        ];
        @endphp
        @foreach ($katalog as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-item {{ request()->routeIs($item['match']) ? 'active' : '' }}"
           :class="{ 'justify-center !mx-0 !w-full !rounded-none px-0': !$store.sidebar.open && $store.sidebar.collapsed }">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $item['svg'] !!}</svg>
            <span x-show="$store.sidebar.showLabels" class="nav-label">{{ $item['label'] }}</span>
        </a>
        @endforeach

        {{-- ── TRANSAKSI ── --}}
        <div x-show="$store.sidebar.showLabels" class="nav-section mt-1">Transaksi</div>

        @php
        $transaksi = [
            ['route' => 'pos.accounting.index', 'match' => 'pos.accounting.*', 'label' => 'Akuntansi',
             'svg' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
            ['route' => 'pos.reports.sales',    'match' => 'pos.reports.*',    'label' => 'Laporan',
             'svg' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
            ['route' => 'pos.purchase-orders.index', 'match' => 'pos.purchase-orders.*', 'label' => 'Purchase Order',
             'svg' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>'],
            ['route' => 'pos.expenses.index', 'match' => 'pos.expenses.*', 'label' => 'Biaya',
             'svg' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
            ['route' => 'pos.reports.expenses', 'match' => 'pos.reports.expenses', 'label' => 'Laporan Biaya',
             'svg' => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83M22 12A10 10 0 0 0 12 2v10z"/>'],
        ];
        @endphp
        @foreach ($transaksi as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-item {{ request()->routeIs($item['match']) ? 'active' : '' }}"
           :class="{ 'justify-center !mx-0 !w-full !rounded-none px-0': !$store.sidebar.open && $store.sidebar.collapsed }">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $item['svg'] !!}</svg>
            <span x-show="$store.sidebar.showLabels" class="nav-label">{{ $item['label'] }}</span>
        </a>
        @endforeach

        {{-- ── PENGATURAN (admin / manager) ── --}}
        @if ($isAdmin || $isManager)
        <div x-show="$store.sidebar.showLabels" class="nav-section mt-1">Pengaturan</div>

        @php
        $pengaturan = [
            ['route' => 'pos.users.index',    'match' => 'pos.users.*',    'label' => 'Pengguna',
             'svg' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
        ];
        @endphp
        @foreach ($pengaturan as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-item {{ request()->routeIs($item['match']) ? 'active' : '' }}"
           :class="{ 'justify-center !mx-0 !w-full !rounded-none px-0': !$store.sidebar.open && $store.sidebar.collapsed }">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">{!! $item['svg'] !!}</svg>
            <span x-show="$store.sidebar.showLabels" class="nav-label">{{ $item['label'] }}</span>
        </a>
        @endforeach
        @endif

    </nav>

    {{-- User footer --}}
    <div class="shrink-0 border-t border-gray-200 p-3">
        @auth
        <div
            class="flex items-center gap-3 rounded-lg p-2 hover:bg-gray-50 transition-colors cursor-pointer"
            :class="{ 'justify-center': !$store.sidebar.open && $store.sidebar.collapsed }"
        >
            <div class="relative shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-[11px] font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <span class="absolute -right-0.5 -bottom-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-green-400"></span>
            </div>
            <div
                x-show="$store.sidebar.showLabels"
                x-transition:enter="transition-opacity duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="min-w-0 flex-1"
            >
                <p class="truncate text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs capitalize text-gray-400">{{ auth()->user()->role ?? 'staff' }}</p>
            </div>
            <div x-show="$store.sidebar.showLabels" class="shrink-0">
                <a href="{{ route('pos.dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                </a>
            </div>
        </div>
        @endauth
    </div>
</aside>
