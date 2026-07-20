@extends('layouts.app')
@section('title', 'Buka Shift')
@section('breadcrumb', 'Buka Shift')

@section('content')
<div class="mx-auto max-w-md">

    {{-- Back --}}
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('pos.shifts.index') }}"
            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <span class="text-xs text-gray-400">Kembali ke daftar shift</span>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- Header card --}}
        <div class="border-b border-gray-100 px-5 py-4 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100">
                <i class="fas fa-play text-emerald-600 text-lg"></i>
            </div>
            <h1 class="text-base font-bold text-gray-900">Buka Shift Baru</h1>
            <p class="mt-0.5 text-xs text-gray-400">Catat uang kas awal di laci untuk memulai shift</p>
        </div>

        {{-- Info kasir --}}
        <div class="mx-5 mt-4 flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2.5">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-orange-400 to-orange-500 text-xs font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-[10px] capitalize text-gray-400">{{ auth()->user()->role ?? 'kasir' }} &middot; {{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('pos.shifts.open.store') }}" class="px-5 py-4 space-y-3">
            @csrf

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                    Kas awal di laci (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                    <input
                        type="number"
                        name="opening_cash"
                        value="{{ old('opening_cash') }}"
                        required
                        min="0"
                        step="1000"
                        autofocus
                        class="w-full rounded-lg border border-gray-200 py-2.5 pl-9 pr-3 font-mono text-base text-gray-900 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition @error('opening_cash') border-red-400 @enderror"
                        placeholder="0"
                    />
                </div>
                @error('opening_cash')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                {{-- Quick amount buttons --}}
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @foreach ([100000, 200000, 300000, 500000] as $amount)
                        <button type="button"
                            onclick="document.querySelector('[name=opening_cash]').value = {{ $amount }}"
                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-600 hover:border-orange-300 hover:text-orange-600 transition-colors">
                            {{ number_format($amount / 1000, 0) }}rb
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700">Catatan (opsional)</label>
                <textarea
                    name="opening_notes"
                    rows="2"
                    placeholder="Misal: kondisi laci, jumlah kembalian awal, dll."
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20 transition resize-none"
                >{{ old('opening_notes') }}</textarea>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-emerald-500 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-600 transition-colors">
                <i class="fas fa-play mr-2"></i> Buka Shift Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
