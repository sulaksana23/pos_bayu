<header
    x-data="navbarData()"
    class="fixed inset-x-0 top-0 z-40 h-[60px] bg-white border-b border-gray-200 shadow-topbar"
    :class="scrolled ? 'shadow-sm' : ''"
    @scroll.window="scrolled = window.scrollY > 4"
>
    <div class="flex h-full items-center justify-between gap-3 px-4 sm:px-5">

        {{-- Left: logo + hamburger + breadcrumb --}}
        <div class="flex min-w-0 items-center gap-1.5">

            {{-- Logo --}}
            <a
                href="{{ route('pos.dashboard') }}"
                class="flex shrink-0 items-center pr-1.5 border-r border-gray-200 mr-1"
            >
                {{-- Desktop --}}
                <div class="hidden lg:flex items-center gap-2">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <defs>
                            <linearGradient id="navLogo" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#fb923c"/>
                                <stop offset="100%" stop-color="#ea580c"/>
                            </linearGradient>
                        </defs>
                        <path d="M16 2L28.7 9.5V24.5L16 32L3.3 24.5V9.5L16 2Z" fill="url(#navLogo)"/>
                        <text x="16" y="21" text-anchor="middle" font-family="'Inter', ui-sans-serif, sans-serif" font-weight="800" font-size="11" letter-spacing="-0.5" fill="white">BT</text>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 leading-tight">Bali<span class="text-orange-500">POS</span></p>
                        <p class="text-[10px] text-gray-400 leading-tight tracking-wide">Balitech</p>
                    </div>
                </div>
                {{-- Mobile --}}
                <div class="lg:hidden flex items-center">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <defs>
                            <linearGradient id="navLogoM" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#fb923c"/>
                                <stop offset="100%" stop-color="#ea580c"/>
                            </linearGradient>
                        </defs>
                        <path d="M16 2L28.7 9.5V24.5L16 32L3.3 24.5V9.5L16 2Z" fill="url(#navLogoM)"/>
                        <text x="16" y="21" text-anchor="middle" font-family="'Inter', ui-sans-serif, sans-serif" font-weight="800" font-size="11" letter-spacing="-0.5" fill="white">BT</text>
                    </svg>
                </div>
            </a>

            {{-- Mobile hamburger (animated) --}}
            <button
                @click="$store.sidebar.toggle()"
                class="topbar-btn lg:hidden relative"
                aria-label="Buka menu"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <g :class="$store.sidebar.open ? 'opacity-0 scale-75' : 'opacity-100 scale-100'" class="transition-all duration-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 18h18" />
                    </g>
                    <g :class="$store.sidebar.open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'" class="transition-all duration-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18l12-12" />
                    </g>
                </svg>
            </button>

            {{-- Desktop collapse toggle (animated) --}}
            <button
                @click="$store.sidebar.toggleCollapse()"
                class="topbar-btn hidden lg:inline-flex relative"
                :title="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
                aria-label="Toggle sidebar"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <g :class="$store.sidebar.collapsed ? 'opacity-0 scale-75' : 'opacity-100 scale-100'" class="transition-all duration-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 18h18" />
                    </g>
                    <g :class="$store.sidebar.collapsed ? 'opacity-100 scale-100' : 'opacity-0 scale-75'" class="transition-all duration-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18l12-12" />
                    </g>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <nav class="hidden sm:flex items-center gap-1.5 text-sm" aria-label="Breadcrumb">
                <a href="{{ route('pos.dashboard') }}" class="text-gray-400 hover:text-orange-500 transition-colors font-medium">
                    <i class="fas fa-house text-xs"></i>
                </a>
                <i class="fas fa-chevron-right text-[9px] text-gray-300"></i>
                @hasSection('breadcrumb')
                    <span class="text-gray-800 font-semibold text-sm">@yield('breadcrumb')</span>
                @else
                    <span class="text-gray-800 font-semibold text-sm">@yield('title', 'Dashboard')</span>
                @endif
            </nav>
        </div>

        {{-- Center: shift status (md+) --}}
        <div class="hidden md:flex flex-1 items-center justify-center">
            @isset ($currentShift)
                @if ($currentShift)
                    <span class="inline-flex items-center gap-1.5 rounded-full
                                 border border-green-200 bg-green-50
                                 px-3 py-1 text-[11px] font-semibold text-green-700">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span>
                        Shift sejak {{ $currentShift->opened_at->format('H:i') }}
                    </span>
                @else
                    <a
                        href="{{ route('pos.shifts.open') }}"
                        class="inline-flex items-center gap-1.5 rounded-full
                               border border-amber-200 bg-amber-50
                               px-3 py-1 text-[11px] font-semibold text-amber-700
                               hover:bg-amber-100 transition-colors duration-150"
                    >
                        <i class="fas fa-circle-exclamation text-[10px]"></i>
                        Buka shift
                    </a>
                @endif
            @endisset
        </div>

        {{-- Right: search + clock + notifications + user --}}
        <div class="flex items-center gap-1 sm:gap-1.5">

            {{-- Search --}}
            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    class="topbar-btn"
                    aria-label="Cari"
                >
                    <i class="fas fa-search text-sm"></i>
                </button>

                {{-- Search dropdown --}}
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-xl border border-gray-200 shadow-dropdown origin-top-right"
                    x-cloak
                >
                    <div class="p-2">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input
                                type="text"
                                x-model="searchQuery"
                                @input.debounce.200ms="doSearch()"
                                @keydown="searchKeydown($event)"
                                placeholder="Cari menu, produk..."
                                class="w-full pl-8 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-400 focus:ring-1 focus:ring-orange-400 focus:bg-white transition"
                                autofocus
                            />
                        </div>
                    </div>
                    <div x-show="searchQuery.length > 0" class="border-t border-gray-100 max-h-64 overflow-y-auto">
                        <template x-if="searchMenus.length === 0">
                            <div class="px-4 py-6 text-center text-sm text-gray-400">
                                <i class="fas fa-search-minus mb-2 text-gray-300 text-lg block"></i>
                                Tidak ada hasil
                            </div>
                        </template>
                        <template x-for="(item, i) in searchMenus" :key="i">
                            <a
                                :href="item.url"
                                :class="searchActive === i ? 'bg-orange-50 text-orange-600' : 'hover:bg-gray-50'"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm transition"
                                @click="open = false; searchQuery = ''"
                            >
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gray-100">
                                    <i :class="item.icon" class="text-xs text-gray-500"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 truncate" x-text="item.label"></p>
                                    <p class="text-xs text-gray-400 truncate" x-text="item.section"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Live clock (desktop) --}}
            <span
                id="live-clock"
                class="hidden lg:block tabular-nums text-[12px] font-medium text-gray-500 px-1.5"
                aria-label="Jam sekarang"
            ></span>

            {{-- Notifications --}}
            <div class="relative" x-data="{ notifOpen: false }">
                <button
                    @click="notifOpen = !notifOpen"
                    class="topbar-btn relative"
                    aria-label="Notifikasi"
                >
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-white"></span>
                </button>

                <div
                    x-show="notifOpen"
                    @click.outside="notifOpen = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 bg-white rounded-xl border border-gray-200 shadow-dropdown origin-top-right"
                    x-cloak
                >
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
                        <button class="text-[11px] text-orange-500 hover:text-orange-600 font-medium">Tandai semua dibaca</button>
                    </div>
                    <div class="py-2">
                        <p class="px-4 py-6 text-center text-sm text-gray-400">
                            <i class="fas fa-bell-slash text-gray-300 text-lg mb-2 block"></i>
                            Belum ada notifikasi
                        </p>
                    </div>
                </div>
            </div>

            {{-- User menu --}}
            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100 transition"
                    aria-label="User menu"
                >
                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-[10px] font-bold text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-semibold text-gray-800 leading-tight">{{ auth()->user()?->name ?? 'User' }}</p>
                        <p class="text-[10px] text-gray-400 capitalize leading-tight">{{ auth()->user()?->role ?? 'staff' }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-[9px] text-gray-400 hidden sm:block"
                       :class="open ? 'rotate-180' : ''"
                       style="transition: transform .2s"></i>
                </button>

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-52 bg-white rounded-xl border border-gray-200 shadow-dropdown origin-top-right py-1"
                    x-cloak
                >
                    {{-- User info --}}
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()?->name }}</p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ auth()->user()?->email }}</p>
                        <span class="mt-1.5 inline-block rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-semibold capitalize text-orange-600">
                            {{ auth()->user()?->role ?? 'staff' }}
                        </span>
                    </div>

                    <a href="{{ route('pos.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-gauge w-4 text-gray-400 text-sm"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('pos.cashier.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-cash-register w-4 text-gray-400 text-sm"></i>
                        Kasir
                    </a>
                    @if (auth()->user()?->isAdmin())
                    <a href="{{ route('pos.users.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-user-shield w-4 text-gray-400 text-sm"></i>
                        Manajemen User
                    </a>
                    @endif

                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-right-from-bracket w-4 text-sm"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
(() => {
    // ── Live clock ──────────────────────────────────────────────────────────
    (function () {
        function tick() {
            const el = document.getElementById('live-clock');
            if (el) {
                const d = new Date();
                el.textContent = d.toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                });
            }
        }
        tick();
        setInterval(tick, 1000);
    })();

    // ── Alpine navbarData ───────────────────────────────────────────────────
    document.addEventListener('alpine:init', () => {
        Alpine.data('navbarData', () => ({
            scrolled: false,
            searchQuery: '',
            searchActive: 0,
            searchMenus: [],

            allMenus: [
                { label: 'Dashboard',    section: 'Operasional', icon: 'fas fa-grid-2',        url: '{{ route("pos.dashboard") }}' },
                { label: 'Kasir',        section: 'Operasional', icon: 'fas fa-cash-register', url: '{{ route("pos.cashier.index") }}' },
                { label: 'Shift',        section: 'Operasional', icon: 'fas fa-clock',         url: '{{ route("pos.shifts.index") }}' },
                { label: 'Produk',       section: 'Katalog',     icon: 'fas fa-box',           url: '{{ route("pos.products.index") }}' },
                { label: 'Kategori',     section: 'Katalog',     icon: 'fas fa-tag',           url: '{{ route("pos.categories.index") }}' },
                { label: 'Stok',         section: 'Katalog',     icon: 'fas fa-warehouse',     url: '{{ route("pos.inventory.index") }}' },
                { label: 'Pelanggan',    section: 'Katalog',     icon: 'fas fa-users',         url: '{{ route("pos.customers.index") }}' },
                { label: 'Akuntansi',    section: 'Transaksi',   icon: 'fas fa-file-invoice',  url: '{{ route("pos.accounting.index") }}' },
                { label: 'Laporan',      section: 'Transaksi',   icon: 'fas fa-chart-bar',     url: '{{ route("pos.reports.sales") }}' },
                @if (auth()->user()?->isAdmin() || auth()->user()?->isManager())
                { label: 'Pengguna',     section: 'Pengaturan',  icon: 'fas fa-user-shield',   url: '{{ route("pos.users.index") }}' },
                @endif
            ],

            doSearch() {
                const q = this.searchQuery.toLowerCase().trim();
                this.searchActive = 0;
                if (!q) { this.searchMenus = []; return; }
                this.searchMenus = this.allMenus
                    .filter(m => m.label.toLowerCase().includes(q) || m.section.toLowerCase().includes(q))
                    .slice(0, 7);
            },

            searchKeydown(e) {
                const r = this.searchMenus;
                if (!r.length) return;
                if (e.key === 'ArrowDown')  { e.preventDefault(); this.searchActive = (this.searchActive + 1) % r.length; }
                else if (e.key === 'ArrowUp') { e.preventDefault(); this.searchActive = (this.searchActive - 1 + r.length) % r.length; }
                else if (e.key === 'Enter')   { e.preventDefault(); if (r[this.searchActive]) window.location.href = r[this.searchActive].url; }
                else if (e.key === 'Escape')  { this.searchQuery = ''; this.searchMenus = []; }
            },
        }));
    });
})();
</script>
