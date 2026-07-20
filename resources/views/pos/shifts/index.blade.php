@extends('layouts.app')
@section('title', 'Shift')
@section('breadcrumb', 'Shift')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Manajemen Shift</h1>
            <p class="text-xs text-gray-400">Riwayat shift yang sudah dibuka & ditutup</p>
        </div>
        @if (!auth()->user()->currentShift())
            <a href="{{ route('pos.shifts.open') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-600 transition-colors">
                <i class="fas fa-play"></i> Buka Shift
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                Shift sedang aktif
            </span>
        @endif
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Kasir</th>
                        <th class="text-right">Opening</th>
                        <th class="text-right">Sales</th>
                        <th class="text-right">Trx</th>
                        <th class="text-right">Selisih</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shifts as $s)
                        <tr>
                            <td>
                                <p class="text-xs font-semibold text-gray-800">{{ $s->opened_at->translatedFormat('d M Y') }}</p>
                                <p class="font-mono text-[10px] text-gray-400">
                                    {{ $s->opened_at->format('H:i') }} → {{ $s->closed_at?->format('H:i') ?? '—' }}
                                </p>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[9px] font-bold text-orange-600">
                                        {{ strtoupper(substr($s->user->name, 0, 2)) }}
                                    </div>
                                    <span class="text-xs text-gray-700">{{ $s->user->name }}</span>
                                </div>
                            </td>
                            <td class="text-right font-mono text-xs">Rp {{ number_format($s->opening_cash, 0, ',', '.') }}</td>
                            <td class="text-right font-mono text-xs font-semibold text-emerald-600">Rp {{ number_format($s->total_sales, 0, ',', '.') }}</td>
                            <td class="text-right font-mono text-xs">{{ $s->transaction_count }}</td>
                            <td class="text-right font-mono text-xs
                                {{ $s->cash_difference === null ? 'text-gray-400' : ((float)$s->cash_difference < 0 ? 'text-red-600 font-semibold' : ((float)$s->cash_difference > 0 ? 'text-emerald-600 font-semibold' : 'text-gray-500')) }}">
                                @if ($s->cash_difference !== null)
                                    {{ (float)$s->cash_difference > 0 ? '+' : '' }}Rp {{ number_format((float)$s->cash_difference, 0, ',', '.') }}
                                @else —
                                @endif
                            </td>
                            <td>
                                @if ($s->status === 'open')
                                    <span class="fb-badge fb-badge-green">
                                        <span class="mr-1 h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="fb-badge fb-badge-gray">Tutup</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('pos.shifts.show', $s) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye text-[10px]"></i> Detail
                                    </a>
                                    @if ($s->status === 'open' && ($s->user_id === auth()->id() || auth()->user()->isAdmin()))
                                        <button
                                            onclick="document.getElementById('closeShift{{ $s->id }}').classList.remove('hidden')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-stop text-[10px]"></i> Tutup
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                                        <i class="fas fa-clock text-xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-400">Belum ada shift</p>
                                    <a href="{{ route('pos.shifts.open') }}" class="text-xs text-orange-500 hover:underline">Buka shift pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($shifts, 'hasPages') && $shifts->hasPages())
            <div class="border-t border-gray-100 px-4 py-3">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>
</div>

@foreach ($shifts->where('status', 'open') as $s)
    @includeWhen(($s->user_id === auth()->id() || auth()->user()->isAdmin()), 'pos.shifts._close_modal', ['shift' => $s])
@endforeach
@endsection
