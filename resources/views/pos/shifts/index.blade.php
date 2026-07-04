@extends ('layouts.app')
@section ('title','Shift')

@section ('content')
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Shift</h1>
                <p class="mt-0.5 text-sm text-gray-500">Riwayat shift yang sudah dibuka & ditutup.</p>
            </div>
            @if (!auth()->user()->currentShift())
                <a
                    href="{{ route('pos.shifts.open') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition-all hover:shadow-xl"
                >
                    <i class="fas fa-play"></i> Buka Shift
                </a>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead
                    class="border-b border-gray-100 bg-gray-50 text-xs tracking-wider text-gray-600 uppercase"
                >
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Kasir</th>
                        <th class="px-4 py-3 text-right">Opening</th>
                        <th class="px-4 py-3 text-right">Sales</th>
                        <th class="px-4 py-3 text-right">Trx</th>
                        <th class="px-4 py-3 text-right">Selisih</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($shifts as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">
                                {{ $s->opened_at->translatedFormat('d M Y') }}<br /><span
                                    class="font-mono text-[11px] text-gray-400"
                                    >{{ $s->opened_at->format('H:i') }} → {{ $s->closed_at?->format('H:i') ?? '—' }}</span
                                >
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $s->user->name }}</td>
                            <td class="px-4 py-3 text-right font-mono">
                                Rp {{ number_format($s->opening_cash,0,',','.') }}
                            </td>
                            <td
                                class="px-4 py-3 text-right font-mono font-semibold text-emerald-600"
                            >
                                Rp {{ number_format($s->total_sales,0,',','.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                {{ $s->transaction_count }}
                            </td>
                            <td
                                class="px-4 py-3 text-right font-mono {{ $s->cash_difference === null ? 'text-gray-400' : ((float)$s->cash_difference < 0 ? 'text-red-600' : ((float)$s->cash_difference > 0 ? 'text-emerald-600' : 'text-gray-500')) }}"
                            >
                                @if ($s->cash_difference !== null)
                                    {{ ((float)$s->cash_difference) > 0 ? '+' : '' }}Rp {{ number_format((float)$s->cash_difference,0,',','.') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase {{ $s->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}"
                                    >{{ $s->status }}</span
                                >
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a
                                    href="{{ route('pos.shifts.show', $s) }}"
                                    class="text-xs font-semibold text-orange-600 hover:underline"
                                    >Detail</a
                                >
                                @if ($s->status === 'open' && ($s->user_id === auth()->id() || auth()->user()->isAdmin()))
                                    <button
                                        onclick="document.getElementById('closeShift{{ $s->id }}').classList.remove('hidden')"
                                        class="ml-2 text-xs font-semibold text-red-600 hover:underline"
                                    >
                                        Tutup
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">
                                Belum ada shift.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($shifts->where('status','open') as $s)
        @includeWhen (($s->user_id === auth()->id() || auth()->user()->isAdmin()), 'pos.shifts._close_modal', ['shift' => $s])
    @endforeach
@endsection
