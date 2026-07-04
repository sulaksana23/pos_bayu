<header class="fixed inset-x-0 top-0 z-30 h-16 border-b border-gray-200 bg-white shadow-sm">
    <div class="flex h-full items-center justify-between gap-3 px-3 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <button
                onclick="window.toggleSidebar()"
                class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                aria-label="Toggle menu"
            >
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('pos.dashboard') }}" class="flex min-w-0 items-center gap-2">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 shadow-md shadow-orange-500/30"
                >
                    <i class="fas fa-cash-register text-sm text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-gray-800">{{ config('app.name', 'GHouse POS') }}</p>
                    <p class="-mt-1 text-[10px] text-gray-400">
                        <span class="font-semibold text-orange-500">v{{ config('app.version', '1.0.0') }}</span>
                        &middot; <span class="hidden sm:inline">by <a href="https://balitechsolution.com" class="font-semibold hover:text-orange-500">Balitech Solution</a></span>
                    </p>
                </div>
            </a>
            @isset ($currentShift)
                @if ($currentShift)
                    <span
                        class="hidden items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 md:inline-flex"
                    >
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                        Shift terbuka sejak {{ $currentShift->opened_at->format('H:i') }}
                    </span>
                @else
                    <a
                        href="{{ route('pos.shifts.open') }}"
                        class="hidden items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 hover:bg-amber-100 md:inline-flex"
                    >
                        <i class="fas fa-circle-exclamation"></i> Buka shift
                    </a>
                @endif
            @endisset
        </div>

        <div class="flex items-center gap-2">
            <div
                class="hidden items-center gap-2 rounded-xl bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 sm:flex"
            >
                <i class="far fa-clock text-gray-400"></i>
                <span id="live-clock">{{ now()->format('H:i:s') }}</span>
            </div>
            @auth
                <div class="relative">
                    <button
                        onclick="window.toggleAccountMenu()"
                        class="flex items-center gap-2 rounded-xl p-1.5 pr-3 transition hover:bg-gray-100"
                        aria-label="Akun"
                    >
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-rose-500 text-sm font-bold text-white shadow-md"
                        >
                            {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden min-w-0 text-left md:block">
                            <p class="max-w-[10rem] truncate text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="-mt-0.5 text-[10px] text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div
                        id="accountMenu"
                        class="absolute top-full right-0 z-40 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl"
                    >
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="truncate text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                        <a
                            href="{{ route('logout') }}"
                            onclick="
                                event.preventDefault();
                                document.getElementById('logout-form').submit();
                            "
                            class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 transition hover:bg-red-50"
                        >
                            <i class="fas fa-sign-out-alt w-4"></i> Logout
                        </a>
                        <form
                            id="logout-form"
                            action="{{ route('logout') }}"
                            method="POST"
                            class="hidden"
                        >
                            @csrf
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
<script>
    setInterval(() => {
        const el = document.getElementById('live-clock');
        if (el) {
            const d = new Date();
            el.textContent = d.toTimeString().split(' ')[0];
        }
    }, 1000);
    window.toggleAccountMenu = () => {
        const m = document.getElementById('accountMenu');
        if (m) m.classList.toggle('hidden');
    };
</script>
