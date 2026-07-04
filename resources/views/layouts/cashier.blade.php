<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        @yield ('title', 'Kasir')
        - {{ config('app.name', 'POS') }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="user-name" content="{{ auth()->user()?->name ?? '' }}" />
    <meta name="user-role" content="{{ auth()->user()?->role ?? '' }}" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        'olsera-blue': '#0066FF',
                        'olsera-blue-dark': '#0052CC',
                        'olsera-blue-light': '#E6F0FF',
                        'olsera-red': '#FF3366',
                        'olsera-pink': '#FF6B9D',
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
    <style>
        [x-cloak] {
            display: none !important;
        }
        .scroll-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .scroll-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        .scroll-thin::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
</head>
<body class="h-full bg-gray-50 font-sans text-gray-900 antialiased">
    {{-- Top Navbar for Cashier --}}
    <nav
        class="no-print fixed top-0 right-0 left-0 z-50 border-b border-gray-200 bg-white shadow-sm"
    >
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                {{-- Left: Logo & Shift Info --}}
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-md"
                        >
                            <i class="fas fa-cash-register text-lg text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">
                                {{ config('app.name', 'POS') }}
                            </h1>
                            <p class="text-xs text-gray-500">Sistem Kasir</p>
                        </div>
                    </div>

                    @if (isset($currentShift) && $currentShift)
                        <div
                            class="hidden items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 md:flex"
                        >
                            <div class="h-2 w-2 animate-pulse rounded-full bg-green-500"></div>
                            <span class="text-xs font-semibold text-green-700">Shift Aktif</span>
                            <span
                                class="text-xs text-green-600"
                                >{{ $currentShift->opened_at->format('H:i') }}</span
                            >
                        </div>
                    @endif
                </div>

                {{-- Center: Quick Stats --}}
                <div class="hidden items-center gap-4 lg:flex">
                    <div class="rounded-lg bg-blue-50 px-4 py-2 text-center">
                        <p class="text-xs text-gray-600">Transaksi Hari Ini</p>
                        <p class="text-lg font-bold text-blue-600" id="todayTransactions">0</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 px-4 py-2 text-center">
                        <p class="text-xs text-gray-600">Total Penjualan</p>
                        <p class="text-lg font-bold text-emerald-600" id="todaySales">Rp 0</p>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Clock --}}
                    <div class="mr-3 hidden text-right md:block">
                        <p class="text-xs text-gray-500">{{ now()->isoFormat('dddd') }}</p>
                        <p class="text-sm font-semibold text-gray-900" id="currentTime">{{ now()->format('H:i:s') }}</p>
                    </div>

                    {{-- Dashboard --}}
                    <a
                        href="{{ route('pos.dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <i class="fas fa-chart-line text-sm"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>

                    {{-- User Menu --}}
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-500 text-xs font-bold text-white"
                            >
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute right-0 z-50 mt-2 w-56 rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                        >
                            <div class="border-b border-gray-100 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a
                                href="{{ route('pos.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <i class="fas fa-tachometer-alt w-4"></i>
                                <span>Dashboard</span>
                            </a>
                            <a
                                href="{{ route('pos.shifts.index') }}"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <i class="fas fa-clock w-4"></i>
                                <span>Kelola Shift</span>
                            </a>
                            <a
                                href="{{ route('pos.accounting.index') }}"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <i class="fas fa-file-invoice w-4"></i>
                                <span>Laporan</span>
                            </a>
                            <div class="mt-1 border-t border-gray-100 pt-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-3 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                    >
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content (Fullscreen) --}}
    <main class="h-screen overflow-hidden pt-[73px]">
        @yield ('content')
    </main>

    {{-- Clock Update Script --}}
    <script>
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
            const timeEl = document.getElementById('currentTime');
            if (timeEl) timeEl.textContent = timeStr;
        }, 1000);
    </script>

    @stack ('scripts')
</body>
</html>
