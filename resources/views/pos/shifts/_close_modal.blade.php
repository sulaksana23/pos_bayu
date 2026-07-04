<div
    id="closeShift{{ $shift->id }}"
    class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/40 p-4"
>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <h3 class="mb-1 text-base font-bold text-gray-900">Tutup Shift #{{ $shift->id }}</h3>
        <p class="mb-4 text-xs text-gray-500">Rekonsiliasi uang tunai di laci.</p>
        <form method="POST" action="{{ route('pos.shifts.close', $shift) }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-[10px] text-gray-500 uppercase">Opening</p>
                    <p class="font-mono font-semibold">Rp {{ number_format($shift->opening_cash,0,',','.') }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 p-3">
                    <p class="text-[10px] text-emerald-700 uppercase">Expected</p>
                    <p class="font-mono font-semibold text-emerald-700">Rp {{ number_format($shift->expected_cash ?? 0,0,',','.') }}</p>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700"
                    >Uang di laci (aktual)</label
                >
                <input
                    type="number"
                    name="closing_cash"
                    required
                    min="0"
                    step="1000"
                    class="w-full rounded-lg border-2 border-gray-200 px-3 py-2.5 font-mono text-lg focus:border-orange-500 focus:outline-none"
                />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700"
                    >Catatan tutup shift</label
                >
                <textarea
                    name="closing_notes"
                    rows="2"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                ></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    onclick="document.getElementById('closeShift{{ $shift->id }}').classList.add('hidden')"
                    class="rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Batal
                </button>
                <button
                    class="rounded-lg bg-red-500 px-4 py-2 text-sm font-bold text-white hover:bg-red-600"
                >
                    Konfirmasi Tutup
                </button>
            </div>
        </form>
    </div>
</div>
