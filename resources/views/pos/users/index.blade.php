@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-4" x-data="{
    view: localStorage.getItem('users_view') || 'table',
    setView(v) { this.view = v; localStorage.setItem('users_view', v); }
}">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Manajemen Pengguna</h1>
            <p class="text-xs text-gray-500">Kelola akun kasir, manajer, dan admin &middot; {{ $users->total() }} pengguna</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Toggle View --}}
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
                <button @click="setView('table')"
                    :class="view === 'table' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                    <i class="fas fa-table mr-1"></i> Tabel
                </button>
                <button @click="setView('grid')"
                    :class="view === 'grid' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                    <i class="fas fa-th-large mr-1"></i> Grid
                </button>
            </div>
            <a href="{{ route('pos.users.create') }}"
                class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-3 py-2 text-xs font-bold text-white shadow-lg shadow-blue-500/30 transition-all hover:shadow-xl">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('pos.users.index') }}"
        class="flex flex-wrap items-center gap-2 rounded-2xl border border-gray-100 bg-white px-3 py-2.5 shadow-sm">
        <div class="relative min-w-0 flex-1">
            <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-xs text-gray-400"></i>
            <input name="q" value="{{ $q }}" placeholder="Cari nama, email, telepon..."
                class="w-full rounded-lg border border-gray-200 py-1.5 pr-3 pl-9 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
        </div>
        <select name="role" class="rounded-lg border border-gray-200 py-1.5 px-3 text-sm text-gray-600 focus:border-blue-500 focus:outline-none">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manajer</option>
            <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
        </select>
        <button type="submit"
            class="rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-600 transition">
            <i class="fas fa-search mr-1"></i> Cari
        </button>
        @if ($q || request('role'))
            <a href="{{ route('pos.users.index') }}"
                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
        @endif
    </form>

    @php
        $roleColors = [
            'admin'   => 'bg-purple-100 text-purple-700',
            'manager' => 'bg-blue-100 text-blue-700',
            'cashier' => 'bg-gray-100 text-gray-700',
        ];
        $roleLabels = ['admin' => 'Admin', 'manager' => 'Manajer', 'cashier' => 'Kasir'];
        $avatarColors = [
            'admin'   => 'from-purple-500 to-indigo-600',
            'manager' => 'from-blue-500 to-cyan-600',
            'cashier' => 'from-gray-400 to-gray-500',
        ];
    @endphp

    {{-- TABLE VIEW --}}
    <div x-show="view === 'table'" x-cloak>
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Pengguna</th>
                            <th class="px-4 py-3 text-left">Kontak</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $avatarColors[$user->role] ?? 'from-gray-400 to-gray-500' }} text-xs font-bold text-white shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                            @if ($user->id === auth()->id())
                                                <span class="text-[10px] font-medium text-blue-500">Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-gray-700">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->phone ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('pos.users.edit', $user) }}"
                                            class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('pos.users.toggle', $user) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    class="rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition-colors {{ $user->is_active ? 'border-amber-200 text-amber-600 hover:bg-amber-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                                    <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('pos.users.destroy', $user) }}"
                                                onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">
                                    <i class="fas fa-users mb-2 block text-3xl text-gray-300"></i>
                                    Tidak ada pengguna ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- GRID VIEW --}}
    <div x-show="view === 'grid'" x-cloak>
        @if ($users->isEmpty())
            <div class="rounded-2xl border border-gray-100 bg-white px-4 py-12 text-center text-sm text-gray-400 shadow-sm">
                <i class="fas fa-users mb-2 block text-3xl text-gray-300"></i>
                Tidak ada pengguna ditemukan.
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($users as $user)
                    <div class="group relative rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md hover:border-gray-200">
                        {{-- Badge self --}}
                        @if ($user->id === auth()->id())
                            <span class="absolute top-3 right-3 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-600">Anda</span>
                        @endif

                        {{-- Avatar + Info --}}
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $avatarColors[$user->role] ?? 'from-gray-400 to-gray-500' }} text-base font-bold text-white shadow-md">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1 pr-6">
                                <p class="truncate text-sm font-bold text-gray-900">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                                @if ($user->phone)
                                    <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                            @if ($user->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Nonaktif
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="mt-3 flex items-center gap-1.5 border-t border-gray-50 pt-3">
                            <a href="{{ route('pos.users.edit', $user) }}"
                                class="flex-1 rounded-lg border border-gray-200 py-1.5 text-center text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('pos.users.toggle', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition {{ $user->is_active ? 'border-amber-200 text-amber-600 hover:bg-amber-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('pos.users.destroy', $user) }}"
                                    onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($users->hasPages())
                <div class="rounded-2xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
