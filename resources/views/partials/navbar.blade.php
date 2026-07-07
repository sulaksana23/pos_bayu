{{--
    navbar.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Features:
      - Sticky top, shadow appears on scroll (Alpine @scroll.window)
      - Left: hamburger (mobile only) → dispatches 'toggle-sidebar' to Alpine store
              + breadcrumb / page title from @yield('page-title') or fallback
      - Center: shift status badge (desktop only)
      - Right: search (collapse to icon on mobile), live clock, notifications,
               user profile dropdown (Alpine x-data + @click.outside)

    Alpine directives used:
      x-data           — local component state (scrolled, searchOpen, notifOpen, userOpen)
      @scroll.window   — detect page scroll to add shadow
      $dispatch        — not needed here; we write directly to $store.sidebar
      @click.outside   — close dropdowns when clicking elsewhere
      x-show/x-cloak  — toggle visibility with transition
      x-transition     — smooth dropdown enter/leave
--}}
<header
    x-data="{
        scrolled:    false,
        searchOpen:  false,
        notifOpen:   false,
        userOpen:    false,
    }"
    @scroll.window="scrolled = (window.scrollY > 4)"
    :class="scrolled ? 'shadow-lg shadow-black/20' : 'shadow-sm shadow-black/5'"
    class="fixed inset-x-0 top-0 z-40 h-16
           border-b border-white/10
           bg-[#0f172a]/95 backdrop-blur-md
           transition-shadow duration-200"
>
    <div class="flex h-full items-center justify-between gap-2 px-3 sm:px-4">

        {{-- ── Left: hamburger + title ─────────────────────────────────── --}}
        <div class="flex min-w-0 items-center gap-2 sm:gap-3">

            {{-- Hamburger — mobile only. Writes directly to Alpine global store. --}}
            <button
                @click="$store.sidebar.toggle()"
                class="flex lg:hidden items-center justify-center h-9 w-9 rounded-lg
                       text-slate-400 hover:bg-white/10 hover:text-white
                       transition-colors duration-150"
                aria-label="Buka menu navigasi"
            >
                {{-- Animated hamburger → X --}}
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Desktop: sidebar collapse toggle (mirrors button inside sidebar) --}}
            <button
                @click="$store.sidebar.toggleCollapse()"
                class="hidden lg:flex items-center justify-center h-9 w-9 rounded-lg
                       text-slate-400 hover:bg-white/10 hover:text-white
                       transition-colors duration-150"
                :title="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
                aria-label="Toggle sidebar"
            >
                <svg
                    :class="{ 'rotate-180': !$store.sidebar.collapsed }"
                    class="h-4 w-4 transition-transform duration-200"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Page title / breadcrumb --}}
            <div class="hidden min-w-0 sm:block">
                <h1 class="truncate text-sm font-semibold text-white">
                    @yield('page-title', config('app.name', 'BaliPOS'))
                </h1>
                {{-- Optional breadcrumb slot --}}
                @hasSection('breadcrumb')
                    <p class="truncate text-[11px] text-slate-400">@yield('breadcrumb')</p>
                @endif
            </div>
        </div>

        {{-- ── Center: shift status (md+) ─────────────────────────────── --}}
        <div class="hidden md:flex flex-1 items-center justify-center">
            @isset ($currentShift)
                @if ($currentShift)
                    {{-- Shift open badge --}}
                    <span class="inline-flex items-center gap-1.5 rounded-full
                                 border border-emerald-500/30 bg-emerald-500/10
                                 px-3 py-1 text-[11px] font-semibold text-emerald-400">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                        Shift sejak {{ $currentShift->opened_at->format('H:i') }}
                    </span>
                @else
                    {{-- No open shift — prompt to open --}}
                    <a
                        href="{{ route('pos.shifts.open') }}"
                        class="inline-flex items-center gap-1.5 rounded-full
                               border border-amber-500/30 bg-amber-500/10
                               px-3 py-1 text-[11px] font-semibold text-amber-400
                               hover:bg-amber-500/20 transition-colors duration-150"
                    >
                        <i class="fas fa-circle-exclamation text-[10px]"></i>
                        Buka shift
                    </a>
                @endif
            @endisset
        </div>

        {{-- ── Right: search, clock, notif, user ──────────────────────── --}}
        <div class="flex shrink-0 items-center gap-1 sm:gap-2">

            {{-- Search — full bar on desktop, icon+overlay on mobile --}}

            {{-- Desktop search bar --}}
            <div class="relative hidden sm:block">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-500 pointer-events-none"></i>
                <input
                    type="search"
                    placeholder="Cari menu, produk…"
                    class="h-9 w-44 rounded-lg border border-white/10
                           bg-white/5 pl-8 pr-3
                           text-[13px] text-slate-300 placeholder-slate-500
                           outline-none ring-0
                           focus:w-56 focus:border-teal-500/50 focus:bg-white/8 focus:ring-1 focus:ring-teal-500/30
                           transition-all duration-200"
                    aria-label="Cari"
                />
            </div>

            {{-- Mobile search icon — expands full-width overlay --}}
            <button
                @click="searchOpen = true"
                class="flex sm:hidden items-center justify-center h-9 w-9 rounded-lg
                       text-slate-400 hover:bg-white/10 hover:text-white
                       transition-colors duration-150"
                aria-label="Buka pencarian"
            >
                <i class="fas fa-search text-sm"></i>
            </button>

            {{-- Mobile search overlay --}}
            <div
                x-show="searchOpen"
                x-transition:enter="transition-all duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition-all duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @keydown.escape.window="searchOpen = false"
                class="absolute inset-x-0 top-0 z-50 flex h-16 items-center gap-2 bg-[#0f172a] px-3 sm:hidden"
                x-cloak
            >
                <i class="fas fa-search text-slate-500"></i>
                <input
                    x-ref="mobileSearch"
                    x-init="$watch('searchOpen', v => v && $nextTick(() => $refs.mobileSearch?.focus()))"
                    type="search"
                    placeholder="Cari menu, produk…"
                    class="flex-1 bg-transparent text-sm text-slate-300 placeholder-slate-500 outline-none"
                    aria-label="Cari (mobile)"
                />
                <button
                    @click="searchOpen = false"
                    class="h-8 w-8 rounded-md text-slate-400 hover:text-white transition-colors"
                    aria-label="Tutup pencarian"
                >
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            {{-- Live clock (desktop only) --}}
            <span
                id="live-clock"
                class="hidden lg:block tabular-nums text-[12px] font-medium text-slate-400 px-1"
                aria-label="Jam sekarang"
            ></span>

            {{-- Notification bell --}}
            <div class="relative" x-data="{ notifOpen: false }">
                <button
                    @click="notifOpen = !notifOpen"
                    class="relative flex items-center justify-center h-9 w-9 rounded-lg
                           text-slate-400 hover:bg-white/10 hover:text-white
                           transition-colors duration-150"
                    aria-label="Notifikasi"
                    :aria-expanded="notifOpen"
                >
                    <i class="fas fa-bell text-sm"></i>
                    {{-- Unread dot — show conditionally if you have unread count --}}
                    <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-[#0f172a]"></span>
                </button>

                {{-- Notification dropdown --}}
                <div
                    x-show="notifOpen"
                    @click.outside="notifOpen = false"
                    x-transition:enter="transition-all duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition-all duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute right-0 top-full mt-2 w-72 rounded-xl
                           border border-white/10 bg-[#1e293b] shadow-xl shadow-black/30"
                    x-cloak
                >
                    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                        <span class="text-sm font-semibold text-white">Notifikasi</span>
                        <button class="text-[11px] text-teal-400 hover:text-teal-300">Tandai semua dibaca</button>
                    </div>
                    <div class="py-2">
                        <p class="px-4 py-6 text-center text-sm text-slate-500">Belum ada notifikasi</p>
                    </div>
                </div>
            </div>

            {{-- Settings shortcut — links to users page (admin) or dashboard --}}
            <a
                href="{{ $isAdmin ?? false ? route('pos.users.index') : route('pos.dashboard') }}"
                class="flex items-center justify-center h-9 w-9 rounded-lg
                       text-slate-400 hover:bg-white/10 hover:text-white
                       transition-colors duration-150"
                aria-label="Pengaturan"
                title="Pengaturan"
            >
                <i class="fas fa-gear text-sm"></i>
            </a>

            {{-- ── User profile dropdown ──────────────────────────────── --}}
            @auth
            <div class="relative" x-data="{ userOpen: false }">
                <button
                    @click="userOpen = !userOpen"
                    class="flex items-center gap-2 rounded-lg px-2 py-1.5
                           hover:bg-white/10 transition-colors duration-150"
                    :aria-expanded="userOpen"
                    aria-label="Menu pengguna"
                >
                    {{-- Avatar initials --}}
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                bg-gradient-to-br from-teal-500 to-teal-600
                                text-[11px] font-bold text-white shadow">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    {{-- Name + role — desktop only --}}
                    <div class="hidden lg:block text-left leading-tight">
                        <p class="max-w-[100px] truncate text-[13px] font-semibold text-white">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-[10px] capitalize text-slate-400">
                            {{ auth()->user()->role ?? 'staff' }}
                        </p>
                    </div>
                    {{-- Chevron --}}
                    <svg
                        :class="{ 'rotate-180': userOpen }"
                        class="hidden lg:block h-3.5 w-3.5 text-slate-500 transition-transform duration-150"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- User dropdown menu --}}
                <div
                    x-show="userOpen"
                    @click.outside="userOpen = false"
                    x-transition:enter="transition-all duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition-all duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute right-0 top-full mt-2 w-52 rounded-xl
                           border border-white/10 bg-[#1e293b] shadow-xl shadow-black/30"
                    x-cloak
                >
                    {{-- User info header --}}
                    <div class="border-b border-white/10 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[11px] text-slate-400">{{ auth()->user()->email }}</p>
                        <span class="mt-1 inline-block rounded-md bg-teal-500/15 px-2 py-0.5 text-[10px] font-semibold capitalize text-teal-400">
                            {{ auth()->user()->role ?? 'staff' }}
                        </span>
                    </div>

                    {{-- Menu items --}}
                    <div class="py-1.5">
                        <a
                            href="{{ route('pos.dashboard') }}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300
                                   hover:bg-white/5 hover:text-white transition-colors duration-100"
                        >
                            <i class="fas fa-user w-4 text-center text-slate-500"></i>
                            Profil saya
                        </a>
                        @if (auth()->user()?->isAdmin())
                        <a
                            href="{{ route('pos.users.index') }}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300
                                   hover:bg-white/5 hover:text-white transition-colors duration-100"
                        >
                            <i class="fas fa-user-shield w-4 text-center text-slate-500"></i>
                            Manajemen User
                        </a>
                        @endif
                    </div>

                    {{-- Logout --}}
                    <div class="border-t border-white/10 py-1.5">
                        <button
                            @click="document.getElementById('logout-form').submit()"
                            class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-red-400
                                   hover:bg-red-500/10 hover:text-red-300 transition-colors duration-100"
                        >
                            <i class="fas fa-sign-out-alt w-4 text-center"></i>
                            Logout
                        </button>
                    </div>
                </div>

                {{-- Hidden logout form --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
            @endauth

        </div>{{-- end right --}}
    </div>
</header>

{{-- Live clock script — updates every second --}}
<script>
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
</script>
