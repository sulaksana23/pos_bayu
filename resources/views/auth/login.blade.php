@extends('layouts.auth', ['title' => 'Masuk'])

@section('content')
<div class="rounded-2xl bg-white p-5 shadow-2xl shadow-orange-900/30">
    <h2 class="mb-0.5 text-sm font-bold text-gray-900">Masuk ke sistem POS</h2>
    <p class="mb-4 text-xs text-gray-500">Gunakan akun admin atau kasir yang sudah didaftarkan.</p>

    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-xs flex items-center gap-2">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            @foreach ($errors->all() as $error)
                <p class="text-red-600 text-xs flex items-center gap-2">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" id="loginForm" class="space-y-3">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-envelope text-xs" aria-hidden="true"></i>
                </span>
                <input id="email" type="email" name="email"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="username"
                    class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg text-xs text-gray-900
                           focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition
                           @error('email') border-red-400 @enderror"
                    placeholder="email@kasir.com">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                </span>
                <input id="password" type="password" name="password"
                    required autocomplete="current-password"
                    class="w-full pl-9 pr-10 py-2.5 border border-gray-300 rounded-lg text-xs text-gray-900
                           focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition
                           @error('password') border-red-400 @enderror"
                    placeholder="••••••••">
                <button type="button" id="togglePassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition"
                    aria-label="Toggle password visibility">
                    <i class="fas fa-eye text-xs" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        {{-- Remember --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember" value="1"
                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500/30 w-3 h-3">
                Ingat saya
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" id="submitBtn"
            class="w-full py-2.5 bg-gradient-to-r from-orange-500 to-rose-500 hover:from-orange-600 hover:to-rose-600
                   text-white font-semibold rounded-lg text-xs shadow-md shadow-orange-500/25
                   transition-all duration-200 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed">
            <i class="fas fa-arrow-right-to-bracket mr-1.5" aria-hidden="true"></i>
            <span id="btnText">Masuk</span>
        </button>
    </form>

    @if(config('app.env') === 'local' || config('app.demo_mode'))
        <div class="mt-4 border-t border-gray-100 pt-3 text-center">
            <p class="text-xs text-gray-400">Akun demo:</p>
            <p class="mt-0.5 font-mono text-xs text-gray-600">admin&#64;balitechsolution.com / password</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const p = document.getElementById('password');
        const i = this.querySelector('i');
        if (p.type === 'password') {
            p.type = 'text';
            i.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            p.type = 'password';
            i.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    document.getElementById('loginForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        document.getElementById('btnText').textContent = 'Memproses...';
    });
</script>
@endpush
