@extends ('layouts.app')
@section ('title','Stok & Gudang')

@section ('content')
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-base font-bold text-gray-900">Stok & Gudang</h1>
                <p class="mt-0.5 text-xs text-gray-400">{{ $stats['total'] }} produk aktif &middot; {{ $stats['low'] }} menipis &middot; {{ $stats['out'] }} habis</p>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('pos.inventory.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition-all hover:shadow-xl"
                >
                    <i class="fas fa-plus"></i> Tambah Produk
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-400 uppercase">Total Produk</p>
                <p class="mt-1 text-2xl font-bold">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 shadow-sm">
                <p class="text-xs text-amber-700 uppercase">Stok Menipis</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ number_format($stats['low']) }}</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 p-4 shadow-sm">
                <p class="text-xs text-red-700 uppercase">Habis</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ number_format($stats['out']) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
                <p class="text-xs text-emerald-700 uppercase">Nilai Stok</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['value'],0,',','.') }}</p>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('pos.inventory.index') }}"
            class="flex flex-col items-stretch gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm lg:flex-row lg:items-end"
        >
            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold text-gray-700">Cari</label>
                <div class="relative">
                    <i
                        class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"
                    ></i>
                    <input
                        name="q"
                        value="{{ $q }}"
                        placeholder="Nama / SKU / barcode"
                        class="w-full rounded-lg border border-gray-200 py-2 pr-3 pl-10 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                    />
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Kategori</label>
                <select
                    name="category"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
                >
                    <option value="0">Semua</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected ($category === $c->id)>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">Stok</label>
                <select
                    name="stock"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none"
                >
                    <option value="">Semua</option>
                    <option value="low" @selected ($stockFilter === 'low')>Menipis</option>
                    <option value="out" @selected ($stockFilter === 'out')>Habis</option>
                </select>
            </div>
            <button
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-500"
            >
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="scroll-thin overflow-x-auto">
                <table class="w-full text-sm">
                    <thead
                        class="border-b border-gray-100 bg-gray-50 text-xs tracking-wider text-gray-700 uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-left">SKU/Barcode</th>
                            <th class="px-4 py-3 text-right">Harga</th>
                            <th class="px-4 py-3 text-right">Stok</th>
                            <th class="px-4 py-3 text-left">Kategori</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($products as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a
                                        href="{{ route('pos.inventory.show', $p) }}"
                                        class="font-semibold text-gray-900 hover:text-orange-600"
                                        >{{ $p->name }}</a
                                    >
                                    @if ($p->is_out_of_stock)
                                        <span
                                            class="ml-2 rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-600"
                                            >HABIS</span
                                        >
                                    @elseif ($p->is_low_stock)
                                        <span
                                            class="ml-2 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700"
                                            >MENIPIS</span
                                        >
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">
                                    {{ $p->sku }}
                                    @if ($p->barcode)
                                        <br
                                        /><span class="text-[10px]">{{ $p->barcode }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    Rp {{ number_format($p->price,0,',','.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="font-mono {{ $p->is_out_of_stock ? 'text-red-600' : ($p->is_low_stock ? 'text-amber-600' : 'text-gray-700') }} font-semibold"
                                        >{{ $p->stock }}</span
                                    >
                                    <span class="text-xs text-gray-400">{{ $p->unit }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    @if ($p->category)
                                        <span
                                            class="rounded-md px-2 py-0.5 font-semibold"
                                            style="background: {{ $p->category->color }}20; color: {{ $p->category->color }}"
                                            >{{ $p->category->name }}</span
                                        >
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a
                                        href="{{ route('pos.inventory.show', $p) }}"
                                        class="text-xs font-semibold text-orange-500 hover:underline"
                                        >Detail</a
                                    >
                                    @if (auth()->user()->canManageInventory())
                                        <a
                                            href="{{ route('pos.inventory.edit', $p) }}"
                                            class="ml-2 text-xs font-semibold text-orange-600 hover:underline"
                                            >Edit</a
                                        >
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="px-4 py-12 text-center text-sm text-gray-400"
                                >
                                    Tidak ada produk.<a
                                        href="{{ route('pos.inventory.create') }}"
                                        class="font-semibold text-orange-600"
                                    >
                                        Tambah</a
                                    >
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $products->links() }}</div>
        </div>
    </div>
@endsection
