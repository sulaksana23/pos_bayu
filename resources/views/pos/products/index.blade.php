@extends ('layouts.app')
@section ('title', 'Manajemen Produk')

@section ('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Produk</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola produk, stok, dan harga</p>
            </div>
            <a
                href="{{ route('pos.products.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 font-semibold text-white shadow-md transition hover:bg-blue-700"
            >
                <i class="fas fa-plus"></i>
                <span>Tambah Produk</span>
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4">
                <i class="fas fa-check-circle mt-0.5 text-xl text-green-600"></i>
                <div class="flex-1">
                    <p class="font-semibold text-green-900">Berhasil!</p>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
                <button
                    onclick="this.parentElement.remove()"
                    class="text-green-600 hover:text-green-800"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                        <i class="fas fa-box text-xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Produk</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $products->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Produk Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $products->where('is_active', true)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100"
                    >
                        <i class="fas fa-exclamation-triangle text-xl text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Stok Rendah</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $products->filter(fn($p) => $p->stock <= $p->min_stock)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100">
                        <i class="fas fa-times-circle text-xl text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Stok Habis</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $products->where('stock', '<=', 0)->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <form
                method="GET"
                action="{{ route('pos.products.index') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-5"
            >
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Cari Produk</label>
                    <div class="relative">
                        <i
                            class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"
                        ></i>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nama, SKU, atau Barcode..."
                            class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Kategori</label>
                    <select
                        name="category_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Stock Status Filter --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status Stok</label>
                    <select
                        name="stock_status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Semua Status</option>
                        <option
                            value="available"
                            {{ request('stock_status') == 'available' ? 'selected' : '' }}
                        >
                            Tersedia
                        </option>
                        <option
                            value="low"
                            {{ request('stock_status') == 'low' ? 'selected' : '' }}
                        >
                            Stok Rendah
                        </option>
                        <option
                            value="out"
                            {{ request('stock_status') == 'out' ? 'selected' : '' }}
                        >
                            Habis
                        </option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700"
                    >
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a
                        href="{{ route('pos.products.index') }}"
                        class="rounded-lg bg-gray-100 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-200"
                    >
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Products Table --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            @if ($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    Produk
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    SKU / Barcode
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    Kategori
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    Harga
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    Stok
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold tracking-wider text-gray-600 uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($products as $product)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100"
                                            >
                                                @if ($product->image_url)
                                                    <img
                                                        src="{{ $product->image_url }}"
                                                        alt="{{ $product->name }}"
                                                        class="h-full w-full object-cover"
                                                    />
                                                @else
                                                    <div
                                                        class="flex h-full w-full items-center justify-center text-gray-400"
                                                    >
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                                @if ($product->unit)
                                                    <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-mono text-sm text-gray-900">{{ $product->sku }}</p>
                                        @if ($product->barcode)
                                            <p class="font-mono text-xs text-gray-500">{{ $product->barcode }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($product->category)
                                            <span
                                                class="inline-flex items-center gap-1 rounded bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700"
                                            >
                                                <i
                                                    class="{{ $product->category->icon }} text-[10px]"
                                                ></i>
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="font-semibold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @if ($product->cost)
                                            <p class="text-xs text-gray-500">HPP: Rp {{ number_format($product->cost, 0, ',', '.') }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                $stockClass = 'text-gray-900';
                                if($product->stock <= 0) $stockClass = 'text-red-600';
                                elseif($product->stock <= $product->min_stock) $stockClass = 'text-yellow-600';
                            @endphp
                                        <p
                                            class="font-bold {{ $stockClass }}"
                                        >{{ $product->stock }}</p>
                                        @if ($product->min_stock)
                                            <p class="text-xs text-gray-500">Min: {{ $product->min_stock }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($product->is_active)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700"
                                            >
                                                <i class="fas fa-check-circle text-[10px]"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600"
                                            >
                                                <i class="fas fa-pause-circle text-[10px]"></i>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <a
                                                href="{{ route('pos.products.show', $product) }}"
                                                class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50"
                                                title="Detail"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a
                                                href="{{ route('pos.products.edit', $product) }}"
                                                class="rounded-lg p-2 text-yellow-600 transition hover:bg-yellow-50"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a
                                                href="{{ route('pos.products.barcode', $product) }}"
                                                class="rounded-lg p-2 text-purple-600 transition hover:bg-purple-50"
                                                title="Barcode"
                                                target="_blank"
                                            >
                                                <i class="fas fa-barcode"></i>
                                            </a>
                                            <form
                                                action="{{ route('pos.products.destroy', $product) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="
                                                    return confirm(
                                                        'Yakin ingin menghapus produk ini?'
                                                    );
                                                "
                                            >
                                                @csrf
                                                @method ('DELETE')
                                                <button
                                                    type="submit"
                                                    class="rounded-lg p-2 text-red-600 transition hover:bg-red-50"
                                                    title="Hapus"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-gray-200 px-4 py-3">{{ $products->links() }}</div>
            @else
                <div class="p-12 text-center">
                    <div
                        class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-100"
                    >
                        <i class="fas fa-box-open text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">Belum Ada Produk</h3>
                    <p class="mb-4 text-gray-600">Mulai tambahkan produk untuk mengelola inventori Anda</p>
                    <a
                        href="{{ route('pos.products.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700"
                    >
                        <i class="fas fa-plus"></i>
                        <span>Tambah Produk Pertama</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
