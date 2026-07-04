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
    class="flex min-h-screen items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-rose-600 p-4 font-sans antialiased"
>
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <div
                class="mb-3 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-xl shadow-orange-900/30"
            >
                <i class="fas fa-cash-register text-2xl text-orange-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">{{ config('app.name', 'POS') }}</h1>
            <p class="text-sm text-white/80">Solusi kasir modern untuk warung & retail</p>
        </div>
        @yield ('content')
        <p class="mt-6 text-center text-xs text-white/70">
            &copy; {{ date('Y') }} Bali Tech Solution &middot;
            <a href="https://balitechsolution.com" class="underline hover:text-white"
                >balitechsolution.com</a
            >
        </p>
    </div>
    @stack ('scripts')
</body>
</html>
