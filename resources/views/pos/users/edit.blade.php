@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<div class="mx-auto max-w-xl space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('pos.users.index') }}"
            class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1>
            <p class="text-sm text-gray-500">{{ $user->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pos.users.update', $user) }}"
        class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- Nama --}}
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-400 @enderror" />
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-400 @enderror" />
                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Telepon --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('phone') border-red-400 @enderror"
                    placeholder="08xx" />
                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Role --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Role <span class="text-red-500">*</span></label>
                <select name="role" required
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('role') border-red-400 @enderror">
                    <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>Kasir</option>
                    <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manajer</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @if ($user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}" />
                    <p class="mt-1 text-xs text-gray-400">Role tidak dapat diubah untuk akun sendiri.</p>
                @endif
                @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- PIN --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">PIN Kasir
                    <span class="font-normal text-gray-400">(4-6 digit, opsional)</span>
                </label>
                <input type="text" name="pin" value="{{ old('pin') }}" maxlength="6" inputmode="numeric"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('pin') border-red-400 @enderror"
                    placeholder="Kosongkan jika tidak diubah" />
                @error('pin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Password Baru
                    <span class="font-normal text-gray-400">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('password') border-red-400 @enderror"
                    placeholder="Min. 8 karakter" />
                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="Ulangi password baru" />
            </div>

            {{-- Status --}}
            @if ($user->id !== auth()->id())
                <div class="sm:col-span-2 flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600" />
                    <label for="is_active" class="text-sm font-medium text-gray-700">Akun aktif (bisa login)</label>
                </div>
            @else
                <input type="hidden" name="is_active" value="1" />
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
            <a href="{{ route('pos.users.index') }}"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-2 text-sm font-bold text-white shadow-sm hover:shadow-md transition-all">
                <i class="fas fa-save mr-1.5"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
