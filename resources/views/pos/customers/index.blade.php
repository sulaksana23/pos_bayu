@extends('layouts.app')
@section('title', 'Pelanggan')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Pelanggan</h1>
            <p class="mt-0.5 text-xs text-gray-400">{{ $customers->total() }} pelanggan terdaftar</p>
        </div>
        <a href="{{ route('pos.customers.create') }}"
            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
            <i class="fas fa-plus"></i> Tambah Pelanggan
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs text-emerald-800">
        <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs text-red-800">
        <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('pos.customers.index') }}"
        class="flex gap-2 rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
        <div class="relative flex-1">
            <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input name="q" value="{{ $q }}" placeholder="Cari nama, telepon, email..."
                class="w-full rounded-lg border border-gray-200 py-2 pr-3 pl-8 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none" />
        </div>
        <button class="rounded-lg bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-orange-500 transition">
            <i class="fas fa-filter mr-1"></i> Cari
        </button>
        @if($q)
        <a href="{{ route('pos.customers.index') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-500 hover:bg-gray-50 transition">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-wider text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Pelanggan</th>
                        <th class="px-4 py-3 text-left">Kontak</th>
                        <th class="px-4 py-3 text-right">Total Transaksi</th>
                        <th class="px-4 py-3 text-right">Total Belanja</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $c)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('pos.customers.show', $c) }}" class="font-semibold text-gray-900 hover:text-orange-600">
                                {{ $c->name }}
                            </a>
                            @if($c->address)
                            <p class="mt-0.5 text-[10px] text-gray-400 truncate max-w-[180px]">{{ $c->address }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($c->phone)
                            <div class="flex items-center gap-1"><i class="fas fa-phone text-gray-400 text-[10px]"></i> {{ $c->phone }}</div>
                            @endif
                            @if($c->email)
                            <div class="flex items-center gap-1 mt-0.5"><i class="fas fa-envelope text-gray-400 text-[10px]"></i> {{ $c->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                {{ number_format($c->transactions_count) }}x
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">
                            Rp {{ number_format($c->transactions_sum_total ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('pos.customers.show', $c) }}"
                                class="rounded-lg border border-gray-200 px-2 py-1 text-[10px] font-medium text-gray-600 hover:bg-gray-50 transition mr-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('pos.customers.edit', $c) }}"
                                class="rounded-lg border border-orange-200 px-2 py-1 text-[10px] font-medium text-orange-600 hover:bg-orange-50 transition mr-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('pos.customers.destroy', $c) }}" class="inline"
                                onsubmit="return confirm('Hapus pelanggan {{ addslashes($c->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-2 py-1 text-[10px] font-medium text-red-500 hover:bg-red-50 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">
                            Belum ada pelanggan.
                            <a href="{{ route('pos.customers.create') }}" class="font-semibold text-orange-600">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $customers->links() }}</div>
    </div>
</div>
@endsection
