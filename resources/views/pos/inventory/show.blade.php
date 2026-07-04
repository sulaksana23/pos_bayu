@extends ('layouts.app')
@section ('title', $inventory->name)

@section ('content')
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.inventory.index') }}" class="rounded-lg p-2 hover:bg-gray-100"
                ><i class="fas fa-arrow-left text-gray-600"></i
            ></a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $inventory->name }}</h1>
                <p class="font-mono text-sm text-gray-500">
                    {{ $inventory->sku }}
                    @if ($inventory->barcode) ·{{ $inventory->barcode }} @endif
                </p>
            </div>
            <div class="flex gap-2">
                @if (auth()->user()->canManageInventory())
                    <a
                        href="{{ route('pos.inventory.edit', $inventory) }}"
                        class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white hover:bg-orange-600"
                        ><i class="fas fa-pen mr-1"></i> Edit</a
                    >
                @endif
                <button
                    onclick="document.getElementById('adjustModal').classList.remove('hidden')"
                    class="rounded-lg bg-emerald-500 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-600"
                >
                    <i class="fas fa-sliders mr-1"></i> Sesuaikan Stok
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-bold text-gray-800">Detail Produk</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Harga Jual</dt>
                        <dd class="font-bold text-orange-600">
                            Rp {{ number_format($inventory->price,0,',','.') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Modal</dt>
                        <dd>
                            {{ $inventory->cost ? 'Rp '.number_format($inventory->cost,0,',','.') : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kategori</dt>
                        <dd>
                            @if ($inventory->category)
                                <span
                                    class="rounded-md px-2 py-0.5 text-xs font-semibold"
                                    style="background: {{ $inventory->category->color }}20; color: {{ $inventory->category->color }}"
                                    >{{ $inventory->category->name }}</span
                                >
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span
                                class="text-xs font-semibold {{ $inventory->is_active ? 'text-emerald-600' : 'text-red-600' }}"
                                >{{ $inventory->is_active ? 'Aktif' : 'Non-aktif' }}</span
                            >
                        </dd>
                    </div>
                </dl>
            </div>

            <div
                class="bg-gradient-to-br {{ $inventory->is_out_of_stock ? 'from-red-500 to-rose-600' : ($inventory->is_low_stock ? 'from-amber-500 to-orange-600' : 'from-emerald-500 to-teal-600') }} rounded-2xl p-5 text-white shadow-lg"
            >
                <p class="text-xs tracking-wider uppercase opacity-80">Stok Saat Ini</p>
                <p class="mt-2 text-5xl font-bold">{{ $inventory->stock }}</p>
                <p class="text-sm opacity-80">{{ $inventory->unit }}</p>
                <div class="mt-4 flex items-center justify-between text-xs">
                    <span>Min: {{ $inventory->min_stock }}</span>
                    @if ($inventory->is_out_of_stock)
                        <span class="rounded-full bg-white/20 px-2 py-0.5">HABIS</span>
                    @elseif ($inventory->is_low_stock)
                        <span class="rounded-full bg-white/20 px-2 py-0.5">MENIPIS</span>
                    @else
                        <span class="rounded-full bg-white/20 px-2 py-0.5">AMAN</span>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-bold text-gray-800">Statistik</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nilai Stok</dt>
                        <dd class="font-semibold">
                            Rp {{ number_format(($inventory->cost ?? $inventory->price) * $inventory->stock,0,',','.') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Item Terjual</dt>
                        <dd>
                            {{ $inventory->transactionItems->where('transaction.status','completed')->sum('qty') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pergerakan</dt>
                        <dd>{{ $inventory->stockMovements->count() }} entri</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 class="text-base font-bold text-gray-800">Riwayat Stok</h3>
                <span class="text-xs text-gray-500"
                    >{{ $movements->count() + app(App\Models\PosStockMovement::class)->count() }} total</span
                >
            </div>
            <div class="scroll-thin max-h-96 divide-y divide-gray-100 overflow-y-auto">
                @forelse ($movements as $m)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div
                            class="h-8 w-8 rounded-lg flex items-center justify-center {{ $m->qty > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}"
                        >
                            <i
                                class="fas fa-{{ $m->qty > 0 ? 'arrow-down' : 'arrow-up' }}-wide-short"
                            ></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-800">
                                {{ ucfirst($m->type) }}
                                <span class="font-mono"
                                    >{{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</span
                                >
                                {{ $inventory->unit }}
                            </p>
                            <p class="text-[11px] text-gray-500">
                                {{ $m->user->name }} &middot; {{ $m->created_at->diffForHumans() }}
                                @if ($m->notes) ·{{ $m->notes }} @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm text-gray-400">
                        Belum ada pergerakan stok.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div
        id="adjustModal"
        class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/40 p-4"
    >
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <h3 class="mb-4 text-base font-bold text-gray-900">Sesuaikan Stok</h3>
            <form
                method="POST"
                action="{{ route('pos.inventory.adjust', $inventory) }}"
                class="space-y-3"
            >
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700"
                        >Tipe Penyesuaian</label
                    >
                    <select
                        name="type"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                    >
                        <option value="in">Stok Masuk (+)</option>
                        <option value="out">Stok Keluar (-)</option>
                        <option value="adjust">Set ke jumlah absolut</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Jumlah</label>
                    <input
                        type="number"
                        name="qty"
                        min="1"
                        required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700"
                        >Harga satuan (opsional)</label
                    >
                    <input
                        type="number"
                        name="unit_cost"
                        min="0"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 font-mono text-sm"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Catatan</label>
                    <textarea
                        name="notes"
                        rows="2"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                    ></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        onclick="document.getElementById('adjustModal').classList.add('hidden')"
                        class="rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Batal
                    </button>
                    <button
                        class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-600"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
