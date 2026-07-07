<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        @yield('title', config('app.name'))
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
                        'olsera-blue':       '#0066FF',
                        'olsera-blue-dark':  '#0052CC',
                        'olsera-blue-light': '#E6F0FF',
                        'olsera-red':        '#FF3366',
                        'olsera-pink':       '#FF6B9D',
                    }
                }
            }
        }
    </script>

    {{--
        Alpine Store — MUST be loaded before Alpine initialises.
        It registers Alpine.store('sidebar') via the `alpine:init` event.
        The <script defer> on Alpine CDN means alpine:init fires before
        Alpine.start(), so the order here is correct:
          1. alpine-stores.js  (sync, registers listener for alpine:init)
          2. alpinejs CDN      (defer, fires alpine:init then starts Alpine)
    --}}
    <script src="{{ asset('js/alpine-stores.js') }}"></script>
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
        /* Hide Alpine-managed elements before hydration */
        [x-cloak] { display: none !important; }

        /* Thin custom scrollbar for sidebar */
        .scroll-thin::-webkit-scrollbar       { width: 4px; height: 4px; }
        .scroll-thin::-webkit-scrollbar-track  { background: transparent; }
        .scroll-thin::-webkit-scrollbar-thumb  { background: #334155; border-radius: 2px; }
        .scroll-thin::-webkit-scrollbar-thumb:hover { background: #475569; }

        @media print {
            .no-print, header, aside, nav, .no-print * { display: none !important; }
            body  { background: white !important; }
            main  { padding: 0 !important; margin: 0 !important; }
        }
    </style>
</head>

{{--
    Body background matches sidebar dark navy so there's no flash of
    white behind the sidebar on paint. Content area uses slate-100.
--}}
<body class="h-full bg-slate-100 font-sans text-gray-900 antialiased">

    @include('partials.navbar', ['currentShift' => $currentShift ?? null])

    <div class="flex min-h-screen pt-16">

        @include('partials.sidebar')

        {{--
            Main content area.
            Margin-left mirrors the sidebar width and responds to collapsed state
            via Alpine binding on $store.sidebar.collapsed.
            On mobile (< lg) there is no left margin since sidebar is off-canvas.
        --}}
        <main
            :class="{
                'lg:ml-[260px]': !$store.sidebar.collapsed,
                'lg:ml-[72px]':   $store.sidebar.collapsed,
            }"
            class="min-w-0 flex-1 transition-all duration-200 ease-in-out"
        >
            <div class="p-4 sm:p-6 lg:p-8">
                @includeWhen(session('success'), 'partials.alert', ['type' => 'success', 'message' => session('success')])
                @includeWhen(session('warning'), 'partials.alert', ['type' => 'warning', 'message' => session('warning')])
                @includeWhen(session('info'),    'partials.alert', ['type' => 'info',    'message' => session('info')])
                @includeWhen(session('error'),   'partials.alert', ['type' => 'error',   'message' => session('error')])

                @yield('content')
            </div>
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
