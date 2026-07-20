@extends('layouts.app')
@section('title', 'Tambah Pengguna')

@section('content')
<div class="mx-auto max-w-2xl space-y-3">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('pos.users.index') }}"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-bold text-white shadow-sm">
                <i class="fas fa-user-plus text-xs"></i>
            </div>
            <div class="min-w-0">
                <h1 class="truncate text-base font-bold text-gray-900">Tambah Pengguna</h1>
                <p class="truncate text-xs text-gray-500">Buat akun baru untuk kasir, manajer, atau admin</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('pos.users.store') }}"
        class="rounded-2xl border border-gray-100 bg-white shadow-sm"
        x-data="{ showPass: false }">
        @csrf

        {{-- Section: Info Dasar --}}
        <div class="border-b border-gray-100 px-4 py-3">
            <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Informasi Dasar</p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                {{-- Nama --}}
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 @error('name') border-red-400 @enderror"
                        placeholder="Contoh: Budi Santoso" />
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 @error('email') border-red-400 @enderror"
                        placeholder="email@contoh.com" />
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Telepon --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 @error('phone') border-red-400 @enderror"
                        placeholder="08xx" />
                    @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Role <span class="text-red-500">*</span></label>
                    <select name="role" required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 @error('role') border-red-400 @enderror">
                        <option value="">Pilih role...</option>
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manajer</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- PIN --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">PIN Kasir
                        <span class="font-normal text-gray-400">(4-6 digit, opsional)</span>
                    </label>
                    <input type="text" name="pin" value="{{ old('pin') }}" maxlength="6" inputmode="numeric"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 @error('pin') border-red-400 @enderror"
                        placeholder="1234" />
                    @error('pin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2">
                    <input type="hidden" name="is_active" value="0" />
                    <label class="inline-flex cursor-pointer items-center gap-2.5">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm font-medium text-gray-700">Akun aktif (bisa login)</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Section: Password --}}
        <div class="px-4 py-3">
            <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Password</p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                {{-- Password --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 pr-9 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 @error('password') border-red-400 @enderror"
                            placeholder="Min. 8 karakter" />
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 pr-9 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            placeholder="Ulangi password" />
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <div class="flex items-center justify-end gap-2 border-t border-gray-100 px-4 py-3">
            <a href="{{ route('pos.users.index') }}"
                class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                class="rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:shadow-md transition-all">
                <i class="fas fa-user-plus mr-1.5"></i> Tambah Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
