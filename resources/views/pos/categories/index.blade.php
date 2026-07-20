@extends('layouts.app')
@section('title', 'Kategori')
@section('breadcrumb', 'Kategori')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Kategori</h1>
            <p class="text-xs text-gray-400">Kelola kategori produk</p>
        </div>
        <a href="{{ route('pos.categories.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3.5 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="hidden sm:table-cell">Warna</th>
                        <th class="hidden md:table-cell">Urutan</th>
                        <th class="hidden md:table-cell">Jumlah Produk</th>
                        <th class="hidden sm:table-cell">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $c)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                @if($c->icon)
                                    <i class="fas {{ $c->icon }} text-sm" style="color: {{ $c->color ?? '#6b7280' }}"></i>
                                @endif
                                <span class="font-semibold text-gray-900">{{ $c->name }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell">
                            <span class="inline-block h-5 w-5 rounded-full border border-gray-200" style="background: {{ $c->color ?? '#e5e7eb' }}"></span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-sm text-gray-700">{{ $c->sort_order }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-sm text-gray-700">{{ $c->products_count }}</span>
                        </td>
                        <td class="hidden sm:table-cell">
                            @if($c->is_active)
                                <span class="fb-badge fb-badge-green">Aktif</span>
                            @else
                                <span class="fb-badge fb-badge-gray">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('pos.categories.edit', $c) }}"
                                   class="rounded-lg p-1.5 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('pos.categories.destroy', $c) }}" method="POST"
                                      onsubmit="return confirm('Hapus kategori ini? Produk dengan kategori ini tidak akan terhapus.')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <i class="fas fa-tag text-3xl text-gray-200 mb-2"></i>
                                <p class="text-sm text-gray-400">Belum ada kategori</p>
                                <a href="{{ route('pos.categories.create') }}"
                                   class="mt-2 text-xs font-semibold text-orange-500 hover:underline">
                                    Tambah kategori sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
