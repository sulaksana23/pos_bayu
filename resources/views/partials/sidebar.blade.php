{{--
    sidebar.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    3 States (controlled via Alpine.store('sidebar')):
      1. Expanded  (~260px)   — desktop default, lg+
      2. Collapsed (~72px)    — icon-only, tooltip on hover, toggle via button
      3. Mobile drawer        — off-canvas overlay, toggle via hamburger in navbar

    Key Alpine directives used:
      :class      — dynamic width / translate classes
      x-show      — show/hide labels & section headings
      x-transition — smooth enter/leave for mobile drawer backdrop
      x-tooltip   — hover tooltip in collapsed state (via title attr + CSS)
      @click.outside — close drawer when clicking backdrop
      $store.sidebar — global state shared with navbar hamburger
--}}
@php
    $isAdmin    = auth()->user()?->isAdmin();
    $isManager  = auth()->user()?->isManager();
    $canManage  = auth()->user()?->canManageInventory();
@endphp

{{-- ── Mobile backdrop overlay ─────────────────────────────────────────── --}}
<div
    x-show="$store.sidebar.open"
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.sidebar.close()"
    class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm lg:hidden"
    x-cloak
    aria-hidden="true"
></div>

{{-- ── Sidebar panel ────────────────────────────────────────────────────── --}}
<aside
    id="appSidebar"
    {{--
        Width transitions:
          Mobile  : translate-x-full (hidden) → translate-x-0 (open)
          Desktop : w-[260px] (expanded) ↔ w-[72px] (collapsed)
        We combine both via :class bindings.
    --}}
    :class="{
        '-translate-x-full': !$store.sidebar.open,
        'translate-x-0':      $store.sidebar.open,
        'w-[260px]':          !$store.sidebar.collapsed,
        'w-[72px]':            $store.sidebar.collapsed,
    }"
    class="scroll-thin fixed top-0 bottom-0 left-0 z-30
           flex flex-col overflow-y-auto overflow-x-hidden
           border-r border-white/10
           bg-[#0f172a]
           transition-all duration-200 ease-in-out
           lg:translate-x-0"
    aria-label="Sidebar navigasi"
>

    {{-- ── Header: logo + collapse toggle ─────────────────────────────── --}}
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-4">

        {{-- Logo / App name — hidden when collapsed --}}
        <a
            href="{{ route('pos.dashboard') }}"
            class="flex min-w-0 items-center gap-2.5"
            :class="{ 'opacity-0 pointer-events-none w-0 overflow-hidden': $store.sidebar.collapsed }"
            style="transition: opacity 150ms, width 200ms"
        >
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg shadow-orange-500/30">
                <i class="fas fa-cash-register text-xs text-white"></i>
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-white">{{ config('app.name', 'BaliPOS') }}</p>
                <p class="text-[10px] text-slate-400">
                    <span class="font-semibold text-orange-400">v{{ config('app.version', '1.0.0') }}</span>
                </p>
            </div>
        </a>

        {{-- Icon-only logo when collapsed --}}
        <a
            href="{{ route('pos.dashboard') }}"
            :class="{ 'flex': $store.sidebar.collapsed, 'hidden': !$store.sidebar.collapsed }"
            class="mx-auto items-center justify-center"
        >
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg shadow-orange-500/30">
                <i class="fas fa-cash-register text-xs text-white"></i>
            </div>
        </a>

        {{-- Desktop collapse toggle (hidden on mobile) --}}
        <button
            @click="$store.sidebar.toggleCollapse()"
            :title="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
            class="hidden lg:flex items-center justify-center h-7 w-7 shrink-0 rounded-md text-slate-400
                   hover:bg-white/10 hover:text-white transition-colors duration-150"
            aria-label="Toggle sidebar"
        >
            {{-- Arrow icon flips based on state --}}
            <svg
                :class="{ 'rotate-180': $store.sidebar.collapsed }"
                class="h-4 w-4 transition-transform duration-200"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    {{-- ── Navigation menu ──────────────────────────────────────────────── --}}
    <nav class="flex-1 space-y-0.5 px-3 py-4" aria-label="Menu utama">

        {{-- ╔══════════════════════════════╗ --}}
        {{-- ║  SEKSI: OPERASIONAL          ║ --}}
        {{-- ╚══════════════════════════════╝ --}}

        {{-- Section label — hidden when collapsed, replaced by thin divider --}}
        <p
            x-show="!$store.sidebar.collapsed"
            x-transition:enter="transition-opacity duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mb-1.5 px-2 pt-1 text-[10px] font-bold uppercase tracking-widest text-slate-500"
        >Operasional</p>
        <div x-show="$store.sidebar.collapsed" class="mb-2 border-t border-white/10"></div>

        @php
            $operasional = [
                ['route' => 'pos.dashboard',      'match' => 'pos.dashboard',   'icon' => 'fas fa-chart-line',    'label' => 'Dashboard'],
                ['route' => 'pos.cashier.index',  'match' => 'pos.cashier.*',   'icon' => 'fas fa-cash-register', 'label' => 'Kasir'],
                ['route' => 'pos.shifts.index',   'match' => 'pos.shifts.*',    'icon' => 'fas fa-business-time', 'label' => 'Shift'],
            ];
        @endphp
        @foreach ($operasional as $item)
            @include('partials.sidebar-link', $item)
        @endforeach

        {{-- ╔══════════════════════════════╗ --}}
        {{-- ║  SEKSI: KATALOG              ║ --}}
        {{-- ╚══════════════════════════════╝ --}}
        <p
            x-show="!$store.sidebar.collapsed"
            x-transition:enter="transition-opacity duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="mb-1.5 px-2 pt-5 text-[10px] font-bold uppercase tracking-widest text-slate-500"
        >Katalog</p>
        <div x-show="$store.sidebar.collapsed" class="my-2 border-t border-white/10"></div>

        @php
            $katalog = [
                ['route' => 'pos.products.index',   'match' => 'pos.products.*',   'icon' => 'fas fa-box',           'label' => 'Produk'],
                ['route' => 'pos.categories.index', 'match' => 'pos.categories.*', 'icon' => 'fas fa-tag',           'label' => 'Kategori'],
                ['route' => 'pos.inventory.index',  'match' => 'pos.inventory.*',  'icon' => 'fas fa-boxes-stacked', 'label' => 'Stok & Gudang'],
                ['route' => 'pos.customers.index',  'match' => 'pos.customers.*',  'icon' => 'fas fa-users',         'label' => 'Pelanggan'],
            ];
        @endphp
        @foreach ($katalog as $item)
            @include('partials.sidebar-link', $item)
        @endforeach

        {{-- ╔══════════════════════════════╗ --}}
        {{-- ║  SEKSI: TRANSAKSI            ║ --}}
        {{-- ╚══════════════════════════════╝ --}}
        <p
            x-show="!$store.sidebar.collapsed"
            x-transition:enter="transition-opacity duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="mb-1.5 px-2 pt-5 text-[10px] font-bold uppercase tracking-widest text-slate-500"
        >Transaksi</p>
        <div x-show="$store.sidebar.collapsed" class="my-2 border-t border-white/10"></div>

        @php
            $transaksi = [
                ['route' => 'pos.accounting.index',      'match' => 'pos.accounting.*',      'icon' => 'fas fa-receipt',     'label' => 'Akuntansi'],
                ['route' => 'pos.accounting.cash-drawer','match' => 'pos.accounting.*',      'icon' => 'fas fa-cash-register','label' => 'Kas'],
                ['route' => 'pos.reports.sales',         'match' => 'pos.reports.*',         'icon' => 'fas fa-chart-bar',   'label' => 'Laporan'],
            ];
        @endphp
        @foreach ($transaksi as $item)
            @include('partials.sidebar-link', $item)
        @endforeach

        {{-- ╔══════════════════════════════╗ --}}
        {{-- ║  SEKSI: PENGATURAN (admin)   ║ --}}
        {{-- ╚══════════════════════════════╝ --}}
        @if ($isAdmin || $isManager)
            <p
                x-show="!$store.sidebar.collapsed"
                x-transition:enter="transition-opacity duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="mb-1.5 px-2 pt-5 text-[10px] font-bold uppercase tracking-widest text-slate-500"
            >Pengaturan</p>
            <div x-show="$store.sidebar.collapsed" class="my-2 border-t border-white/10"></div>

            @php
                $pengaturan = [
                    ['route' => 'pos.users.index', 'match' => 'pos.users.*', 'icon' => 'fas fa-user-shield', 'label' => 'Pengguna'],
                ];
            @endphp
            @foreach ($pengaturan as $item)
                @include('partials.sidebar-link', $item)
            @endforeach
        @endif

    </nav>

    {{-- ── Footer: shift status + user mini ─────────────────────────────── --}}
    <div class="shrink-0 border-t border-white/10 p-3">
        @auth
            <div
                class="flex items-center gap-3 rounded-lg px-2 py-2"
                :class="{ 'justify-center': $store.sidebar.collapsed }"
            >
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-teal-600 text-xs font-bold text-white shadow">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span class="absolute -right-0.5 -bottom-0.5 h-2.5 w-2.5 rounded-full border-2 border-[#0f172a] bg-emerald-400"></span>
                </div>

                {{-- Name + role — hidden when collapsed --}}
                <div
                    x-show="!$store.sidebar.collapsed"
                    x-transition:enter="transition-opacity duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="min-w-0"
                >
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[11px] capitalize text-slate-400">{{ auth()->user()->role ?? 'staff' }}</p>
                </div>
            </div>
        @endauth
    </div>

</aside>

{{--
    ── Sidebar link sub-partial ──────────────────────────────────────────────
    Extracted inline below via @include('partials.sidebar-link', [...])
    Variables: $route, $match, $icon, $label
    Active detection: request()->routeIs($match)
    Active style: left border teal accent + soft teal tint bg (no solid block)
--}}
