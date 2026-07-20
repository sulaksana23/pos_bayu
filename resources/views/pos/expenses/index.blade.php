@php
    $isAdmin = auth()->user()?->isAdmin();
@endphp
@extends('layouts.app')
@section('title', 'Biaya')
@section('breadcrumb', 'Biaya')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Biaya Operasional</h1>
            <p class="text-xs text-gray-400">Catat dan kelola biaya operasional bisnis</p>
        </div>
        <a href="{{ route('pos.expenses.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3.5 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
            <i class="fas fa-plus"></i> Catat Biaya
        </a>
    </div>

    {{-- Filter & Total --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pos.expenses.index') }}"
                   class="rounded-lg px-2.5 py-1 text-xs font-semibold transition {{ !request('category') ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua
                </a>
                @foreach($categories as $key => $label)
                <a href="{{ route('pos.expenses.index', array_merge(request()->query(), ['category' => $key])) }}"
                   class="rounded-lg px-2.5 py-1 text-xs font-semibold transition {{ request('category') === $key ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ explode(' ', $label)[0] }}
                </a>
                @endforeach
            </div>
            <div class="text-right">
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Total Biaya</p>
                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Date filter --}}
        <form method="GET" action="{{ route('pos.expenses.index') }}" class="mt-3 flex flex-wrap items-end gap-2">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
            </div>
            <button type="submit"
                    class="rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-200 transition">
                <i class="fas fa-filter mr-0.5"></i> Filter
            </button>
            @if(request()->anyFilled(['date_from', 'date_to', 'category']))
            <a href="{{ route('pos.expenses.index') }}"
               class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-semibold text-gray-500 hover:bg-gray-50 transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>No. Biaya</th>
                        <th class="hidden sm:table-cell">Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                        <th class="hidden md:table-cell">Tanggal</th>
                        <th class="hidden lg:table-cell">Pembayaran</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-900 text-sm">{{ $exp->expense_no }}</p>
                            <p class="text-[11px] text-gray-400">{{ $exp->user->name }}</p>
                        </td>
                        <td class="hidden sm:table-cell">
                            <span class="fb-badge {{ match($exp->category) {
                                'operational' => 'fb-badge-blue',
                                'utilities' => 'fb-badge-yellow',
                                'rent' => 'fb-badge-purple',
                                'salary' => 'fb-badge-green',
                                'maintenance' => 'fb-badge-orange',
                                'marketing' => 'fb-badge-red',
                                default => 'fb-badge-gray',
                            } }}">
                                {{ $categories[$exp->category] ?? $exp->category }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm text-gray-700">{{ $exp->description ?: '-' }}</span>
                        </td>
                        <td class="text-right">
                            <span class="text-sm font-bold text-red-600">-Rp {{ number_format($exp->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-sm text-gray-700">{{ $exp->expense_date->format('d/m/Y') }}</span>
                        </td>
                        <td class="hidden lg:table-cell">
                            <span class="text-sm capitalize text-gray-700">{{ $exp->payment_method ?: '-' }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('pos.expenses.edit', $exp) }}"
                                   class="rounded-lg p-1.5 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @if($isAdmin)
                                <form action="{{ route('pos.expenses.destroy', $exp) }}" method="POST"
                                      onsubmit="return confirm('Hapus biaya ini?')" class="inline">
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
                        <td colspan="7">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <i class="fas fa-receipt text-3xl text-gray-200 mb-2"></i>
                                <p class="text-sm text-gray-400">Belum ada biaya tercatat</p>
                                <a href="{{ route('pos.expenses.create') }}"
                                   class="mt-2 text-xs font-semibold text-orange-500 hover:underline">
                                    Catat biaya sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
