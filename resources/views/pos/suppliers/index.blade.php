@php
    $isAdmin = auth()->user()?->isAdmin();
@endphp
@extends('layouts.app')
@section('title', 'Supplier')
@section('breadcrumb', 'Supplier')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Supplier</h1>
            <p class="text-xs text-gray-400">Kelola data pemasok barang</p>
        </div>
        <a href="{{ route('pos.suppliers.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3.5 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
            <i class="fas fa-plus"></i> Tambah Supplier
        </a>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="hidden md:table-cell">Perusahaan</th>
                        <th class="hidden lg:table-cell">Kontak</th>
                        <th class="hidden lg:table-cell">PIC</th>
                        <th class="hidden sm:table-cell">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td>
                            <a href="{{ route('pos.suppliers.show', $s) }}" class="hover:text-orange-600 transition">
                                <p class="font-semibold text-gray-900 hover:text-orange-600">{{ $s->name }}</p>
                            </a>
                            <p class="text-[11px] text-gray-400">{{ $s->email ?: '-' }}</p>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-sm text-gray-700">{{ $s->company ?: '-' }}</span>
                        </td>
                        <td class="hidden lg:table-cell">
                            <span class="text-sm text-gray-700">{{ $s->phone ?: '-' }}</span>
                        </td>
                        <td class="hidden lg:table-cell">
                            <span class="text-sm text-gray-700">{{ $s->pic_name ?: '-' }}</span>
                        </td>
                        <td class="hidden sm:table-cell">
                            @if($s->is_active)
                                <span class="fb-badge fb-badge-green">Aktif</span>
                            @else
                                <span class="fb-badge fb-badge-gray">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('pos.suppliers.show', $s) }}"
                                   class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('pos.suppliers.edit', $s) }}"
                                   class="rounded-lg p-1.5 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @if($isAdmin)
                                <form action="{{ route('pos.suppliers.toggle', $s) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                                        <i class="fas {{ $s->is_active ? 'fa-toggle-on text-emerald-500' : 'fa-toggle-off' }} text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('pos.suppliers.destroy', $s) }}" method="POST"
                                      onsubmit="return confirm('Hapus supplier ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <i class="fas fa-truck text-3xl text-gray-200 mb-2"></i>
                                <p class="text-sm text-gray-400">Belum ada supplier</p>
                                <a href="{{ route('pos.suppliers.create') }}"
                                   class="mt-2 text-xs font-semibold text-orange-500 hover:underline">
                                    Tambah supplier sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
