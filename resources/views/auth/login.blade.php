@extends ('layouts.auth', ['title' => 'Masuk'])
@section ('content')
    <div class="rounded-2xl bg-white p-6 shadow-2xl shadow-orange-900/30 backdrop-blur sm:p-8">
        <h2 class="mb-1 text-xl font-bold text-gray-900">Masuk ke sistem POS</h2>
        <p class="mb-6 text-sm text-gray-500">Gunakan akun admin/kasir yang sudah didaftarkan.</p>
        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                        ><i class="fas fa-envelope"></i
                    ></span>
                    <input
                        name="email"
                        type="email"
                        value="{{ old('email','admin@balitechsolution.com') }}"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full rounded-xl border border-gray-300 py-2.5 pr-3 pl-10 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                    />
                </div>
                @error ('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                        ><i class="fas fa-lock"></i
                    ></span>
                    <input
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full rounded-xl border border-gray-300 py-2.5 pr-3 pl-10 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                    />
                </div>
                @error ('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" value="1" class="rounded text-orange-500" />
                Ingat saya
            </label>
            <button
                type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 py-2.5 font-semibold text-white transition-all hover:shadow-lg hover:shadow-rose-500/30"
            >
                <i class="fas fa-arrow-right-to-bracket mr-1.5"></i> Masuk
            </button>
        </form>
        <div class="mt-6 border-t border-gray-100 pt-5 text-center">
            <p class="text-xs text-gray-500">Akun demo:</p>
            <p class="mt-1 font-mono text-xs text-gray-700">admin&#64;balitechsolution.com / password</p>
        </div>
    </div>
@endsection
