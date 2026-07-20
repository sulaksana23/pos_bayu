@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('breadcrumb', 'Pengguna')

@section('content')
<div class="space-y-4" x-data="{
    view: localStorage.getItem('users_view') || 'table',
    setView(v) { this.view = v; localStorage.setItem('users_view', v); }
}">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Manajemen Pengguna</h1>
            <p class="text-xs text-gray-400">{{ $users->total() }} pengguna terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Toggle View --}}
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                <button @click="setView('table')"
                    :class="view === 'table' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                    <i class="fas fa-table-list mr-1"></i> Tabel
                </button>
                <button @click="setView('grid')"
                    :class="view === 'grid' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                    <i class="fas fa-grid-2 mr-1"></i> Grid
                </button>
            </div>
            <a href="{{ route('pos.users.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-orange-600 transition-colors">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('pos.users.index') }}"
        class="flex flex-wrap items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2.5 shadow-sm">
        <div class="relative min-w-0 flex-1">
            <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
            <input name="q" value="{{ $q }}" placeholder="Cari nama, email, telepon..."
                class="w-full rounded-lg border border-gray-200 py-1.5 pr-3 pl-8 text-sm text-gray-900 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition" />
        </div>
        <select name="role" class="rounded-lg border border-gray-200 bg-white py-1.5 px-3 text-sm text-gray-600 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition">
            <option value="">Semua Role</option>
            <option value="admin"   {{ request('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
            <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manajer</option>
            <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
        </select>
        <button type="submit"
            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-orange-600 transition-colors">
            <i class="fas fa-search"></i> Cari
        </button>
        @if ($q || request('role'))
            <a href="{{ route('pos.users.index') }}"
                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>

    @php
        $roleColors = [
            'admin'   => 'bg-purple-100 text-purple-700',
            'manager' => 'bg-blue-100 text-blue-700',
            'cashier' => 'bg-gray-100 text-gray-600',
        ];
        $roleLabels = ['admin' => 'Admin', 'manager' => 'Manajer', 'cashier' => 'Kasir'];
        $avatarColors = [
            'admin'   => 'from-purple-500 to-indigo-600',
            'manager' => 'from-blue-500 to-cyan-600',
            'cashier' => 'from-orange-400 to-orange-500',
        ];
    @endphp

    {{-- TABLE VIEW --}}
    <div x-show="view === 'table'" x-cloak>
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="fb-table">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Kontak</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $avatarColors[$user->role] ?? 'from-gray-400 to-gray-500' }} text-xs font-bold text-white">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                            @if ($user->id === auth()->id())
                                                <span class="text-[10px] font-medium text-orange-500">Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs text-gray-700">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->phone ?? '-' }}</p>
                                </td>
                                <td>
                                    <span class="fb-badge {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="fb-badge fb-badge-green">
                                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="fb-badge fb-badge-red">
                                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('pos.users.edit', $user) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-edit text-[11px]"></i> Edit
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('pos.users.toggle', $user) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors
                                                    {{ $user->is_active
                                                        ? 'border-amber-200 text-amber-600 hover:bg-amber-50'
                                                        : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                                    <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }} text-[11px]"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('pos.users.destroy', $user) }}"
                                                onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                    <i class="fas fa-trash text-[11px]"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                                            <i class="fas fa-users text-xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-400">Tidak ada pengguna ditemukan</p>
                                        @if ($q || request('role'))
                                            <a href="{{ route('pos.users.index') }}" class="text-xs text-orange-500 hover:underline">Hapus filter</a>
                                        @endif
                                    </div>
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
            <div class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-white py-14 text-center shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                    <i class="fas fa-users text-xl text-gray-300"></i>
                </div>
                <p class="text-sm font-medium text-gray-400">Tidak ada pengguna ditemukan</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($users as $user)
                    <div class="group relative rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all hover:shadow-card-lg hover:border-gray-300">
                        @if ($user->id === auth()->id())
                            <span class="absolute top-3 right-3 rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-bold text-orange-600">Anda</span>
                        @endif

                        {{-- Avatar + Info --}}
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $avatarColors[$user->role] ?? 'from-gray-400 to-gray-500' }} text-sm font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1 {{ $user->id === auth()->id() ? 'pr-10' : '' }}">
                                <p class="truncate text-sm font-bold text-gray-900">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                                @if ($user->phone)
                                    <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="fb-badge {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                            @if ($user->is_active)
                                <span class="fb-badge fb-badge-green">
                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="fb-badge fb-badge-red">
                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-400"></span> Nonaktif
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="mt-3 flex items-center gap-1.5 border-t border-gray-100 pt-3">
                            <a href="{{ route('pos.users.edit', $user) }}"
                                class="flex-1 rounded-lg border border-gray-200 py-1.5 text-center text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('pos.users.toggle', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors
                                        {{ $user->is_active
                                            ? 'border-amber-200 text-amber-600 hover:bg-amber-50'
                                            : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('pos.users.destroy', $user) }}"
                                    onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($users->hasPages())
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
