@extends ('layouts.app')
@section ('title','Shift #' . $shift->id)

@section ('content')
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.shifts.index') }}" class="rounded-lg p-2 hover:bg-gray-100"
                ><i class="fas fa-arrow-left text-gray-600"></i
            ></a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Shift #{{ $shift->id }}</h1>
                <p class="text-sm text-gray-500">{{ $shift->user->name }} &middot; {{ $shift->opened_at->translatedFormat('d M Y H:i') }} &rarr; {{ $shift->closed_at?->translatedFormat('H:i') ?? '(masih buka)' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm lg:grid-cols-5">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] text-gray-400 uppercase">Opening Cash</p>
                <p class="mt-1.5 font-mono text-lg font-bold">Rp {{ number_format($shift->opening_cash,0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
                <p class="text-[10px] text-emerald-700 uppercase">Total Sales</p>
                <p class="mt-1.5 font-mono text-lg font-bold text-emerald-600">Rp {{ number_format($shift->total_sales,0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm">
                <p class="text-[10px] text-blue-700 uppercase">Cash</p>
                <p class="mt-1.5 font-mono text-lg font-bold text-blue-600">Rp {{ number_format($shift->total_cash,0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-violet-100 bg-violet-50 p-4 shadow-sm">
                <p class="text-[10px] text-violet-700 uppercase">Non-Cash</p>
                <p class="mt-1.5 font-mono text-lg font-bold text-violet-600">Rp {{ number_format($shift->total_non_cash,0,',','.') }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 shadow-sm">
                <p class="text-[10px] text-amber-700 uppercase">Trx</p>
                <p class="mt-1.5 font-mono text-lg font-bold text-amber-600">{{ $shift->transaction_count }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 class="text-base font-bold text-gray-800">Transaksi Shift Ini</h3>
                <span class="text-xs text-gray-500">{{ $shift->transactions->count() }} item</span>
            </div>
            <table class="w-full text-sm">
                <thead
                    class="border-b border-gray-100 bg-gray-50 text-xs tracking-wider text-gray-600 uppercase"
                >
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Kasir</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($shift->transactions as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $t->invoice_no }}</td>
                            <td class="px-4 py-3">{{ $t->customer?->name ?? 'Umum' }}</td>
                            <td class="px-4 py-3">{{ $t->cashier?->name }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold">
                                Rp {{ number_format($t->total,0,',','.') }}
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">
                                {{ $t->payment_method }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $t->created_at->format('H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">
                                Belum ada transaksi di shift ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
