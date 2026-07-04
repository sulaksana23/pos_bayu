@extends ('layouts.app')
@section ('title','Buka Shift')

@section ('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 text-center">
                <div
                    class="mb-3 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600"
                >
                    <i class="fas fa-play text-2xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Buka Shift Baru</h1>
                <p class="mt-1 text-sm text-gray-500">Catat uang kas awal di laci untuk memulai shift.</p>
            </div>
            <form method="POST" action="{{ route('pos.shifts.open.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700"
                        >Kas awal di laci (Rp) *</label
                    >
                    <input
                        type="number"
                        name="opening_cash"
                        required
                        min="0"
                        step="1000"
                        autofocus
                        class="w-full rounded-lg border-2 border-gray-200 px-3 py-2.5 font-mono text-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 focus:outline-none"
                    />
                    @error ('opening_cash')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700"
                        >Catatan (opsional)</label
                    >
                    <textarea
                        name="opening_notes"
                        rows="2"
                        placeholder="Misal: kondisi laci, jumlah kembalian awal, dll."
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                    ></textarea>
                </div>
                <button
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 py-2.5 font-bold text-white shadow-lg shadow-emerald-500/30 transition-all hover:shadow-xl"
                >
                    <i class="fas fa-play mr-2"></i> Buka Shift Sekarang
                </button>
            </form>
        </div>
    </div>
@endsection
