<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>{{ $title ?? 'Login' }} - {{ config('app.name', 'POS') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
</head>
<body
    class="flex min-h-screen items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-rose-600 px-4 py-6 font-sans antialiased"
>
    <div class="w-full max-w-xs sm:max-w-sm">
        <!-- Header compact -->
        <div class="mb-4 flex items-center gap-3">
            <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-lg shadow-orange-900/30">
                <i class="fas fa-cash-register text-base text-orange-600"></i>
            </div>
            <div class="text-left">
                <h1 class="text-base font-bold leading-tight text-white">{{ config('app.name', 'POS') }}</h1>
                <p class="text-xs text-white/75 leading-tight">Kasir modern untuk warung & retail</p>
            </div>
        </div>

        @yield ('content')

        <p class="mt-4 text-center text-xs text-white/60">
            &copy; {{ date('Y') }} Bali Tech Solution &middot;
            <a href="https://balitechsolution.com" class="underline hover:text-white/90">balitechsolution.com</a>
        </p>
    </div>
    @stack ('scripts')
</body>
</html>
