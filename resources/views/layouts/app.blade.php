<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name')) — {{ config('app.name', 'BaliPOS') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="user-name" content="{{ auth()->user()?->name ?? '' }}" />
    <meta name="user-role" content="{{ auth()->user()?->role ?? '' }}" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa',
                            300: '#fdba74', 400: '#fb923c', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 800: '#9a3412', 900: '#7c2d12'
                        },
                        sidebar: {
                            bg:     '#ffffff',
                            hover:  '#f9fafb',
                            active: '#fff7ed',
                            border: '#e5e7eb',
                            text:   '#6b7280',
                            'text-active': '#111827',
                            section:'#9ca3af',
                        },
                    },
                    boxShadow: {
                        'card':    '0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px -1px rgba(0,0,0,.1)',
                        'card-lg': '0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1)',
                        'topbar':  '0 1px 3px 0 rgba(0,0,0,.08)',
                        'dropdown': '0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1)',
                    },
                }
            }
        }
    </script>

    {{-- Alpine Store — inline (tidak perlu alpine-stores.js) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                open: false,
                collapsed: (() => {
                    try {
                        const saved = localStorage.getItem('pos_sidebar_collapsed');
                        if (saved !== null) return saved === 'true';
                    } catch(e) {}
                    return false;
                })(),
                get showLabels() {
                    return !this.collapsed || this.open;
                },
                toggle() { this.open = !this.open; },
                close() { this.open = false; },
                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                    try { localStorage.setItem('pos_sidebar_collapsed', this.collapsed); } catch(e) {}
                },
            });
        });
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Global fetch interceptor: redirect to login on session expiry --}}
    <script>
    (function() {
        const orig = window.fetch;
        window.fetch = function() {
            return orig.apply(this, arguments).then(res => {
                if (res.status === 401 || res.status === 419) {
                    window.location.href = '/login';
                }
                return res;
            });
        };
    })();
    </script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar       { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track  { background: transparent; }
        ::-webkit-scrollbar-thumb  { background: #d1d5db; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* ── Sidebar nav items ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1px 8px;
            padding: 7px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            cursor: pointer;
            border: none;
            background: none;
            width: calc(100% - 16px);
            text-align: left;
        }
        .nav-item:hover {
            background-color: #f9fafb;
            color: #111827;
        }
        .nav-item.active {
            background-color: #fff7ed;
            color: #111827;
            font-weight: 600;
        }
        .nav-item.active .nav-icon {
            color: #f97316;
        }
        .nav-icon {
            font-size: 14px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
            color: #9ca3af;
            transition: color 0.15s;
        }
        .nav-item:hover .nav-icon { color: #6b7280; }
        .nav-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .nav-chevron {
            font-size: 10px;
            color: #9ca3af;
            transition: transform 0.2s;
        }
        [aria-expanded="true"] .nav-chevron { transform: rotate(90deg); }

        /* ── Section headers ── */
        .nav-section {
            padding: 14px 18px 5px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        /* ── Sub-menu ── */
        .nav-sub .nav-item {
            padding-left: 36px;
            font-size: 12px;
            color: #9ca3af;
        }
        .nav-sub .nav-item:hover { background-color: #f9fafb; color: #111827; }
        .nav-sub .nav-item.active { color: #f97316; background: #fff7ed; font-weight: 600; }

        /* ── Topbar button ── */
        .topbar-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            color: #6b7280;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .topbar-btn:hover { background: #f3f4f6; color: #111827; }

        /* ── Card ── */
        .fb-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }
        .fb-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        /* ── Table ── */
        .fb-table { width: 100%; border-collapse: collapse; }
        .fb-table thead tr { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        .fb-table thead th { padding: 10px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; text-align: left; }
        .fb-table tbody tr { border-bottom: 1px solid #f3f4f6; transition: background 0.1s; }
        .fb-table tbody tr:hover { background: #f9fafb; }
        .fb-table tbody td { padding: 10px 12px; font-size: 12.5px; color: #374151; }
        .fb-table tbody tr:last-child { border-bottom: none; }

        /* ── Badge ── */
        .fb-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .fb-badge-green  { background: #dcfce7; color: #166534; }
        .fb-badge-red    { background: #fee2e2; color: #991b1b; }
        .fb-badge-yellow { background: #fef9c3; color: #854d0e; }
        .fb-badge-blue   { background: #dbeafe; color: #1e40af; }
        .fb-badge-orange { background: #ffedd5; color: #c2410c; }
        .fb-badge-purple { background: #f3e8ff; color: #6b21a8; }
        .fb-badge-gray   { background: #f3f4f6; color: #374151; }

        /* ── Page content ── */
        .page-content { min-height: calc(100vh - 60px); }

        /* ── Chart containers ── */
        .chart-container    { height: 260px; position: relative; }
        .chart-container-sm { height: 200px; position: relative; }

        @media print {
            .no-print, header, aside, nav, .no-print * { display: none !important; }
            body  { background: white !important; }
            main  { padding: 0 !important; margin: 0 !important; }
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-gray-50 font-sans antialiased text-gray-900">

    <div class="flex h-full" x-data>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main wrapper --}}
        <div
            class="flex flex-1 flex-col min-w-0 transition-all duration-200"
            :class="{
                'lg:pl-[260px]': !$store.sidebar.collapsed,
                'lg:pl-[68px]':   $store.sidebar.collapsed,
            }"
        >
            {{-- Topbar --}}
            @include('partials.navbar', ['currentShift' => $currentShift ?? null])

            {{-- Content --}}
            <main class="flex-1 pt-[60px]">
                <div class="page-content p-4 sm:p-5 lg:p-6">
                    @includeWhen(session('success'), 'partials.alert', ['type' => 'success', 'message' => session('success')])
                    @includeWhen(session('error'),   'partials.alert', ['type' => 'error',   'message' => session('error')])
                    @includeWhen(session('warning'), 'partials.alert', ['type' => 'warning', 'message' => session('warning')])
                    @includeWhen(session('info'),    'partials.alert', ['type' => 'info',    'message' => session('info')])

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
