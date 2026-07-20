@php
    $pageTitle = 'Demo BaliPOS — Kasir';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f9fafb; }
    </style>
</head>
<body>
    {{-- Demo Banner --}}
    <div class="bg-gradient-to-r from-orange-500 to-rose-500 px-4 py-2 text-center text-xs font-semibold text-white">
        <i class="fas fa-info-circle mr-1"></i> DEMO — Data bersifat contoh. <a href="{{ route('login') }}" class="underline">Login</a> untuk menggunakan POS sesungguhnya.
    </div>

    {{-- Demo Cashier Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-rose-500 text-sm font-bold text-white">
                <i class="fas fa-cash-register"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900">Demo Kasir</p>
                <p class="text-[10px] text-gray-400">{{ $openShift->shift_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 text-xs text-gray-500">
            <span><i class="far fa-clock mr-1"></i> {{ $openShift->opened_at }}</span>
            <span class="rounded-lg bg-orange-50 px-2 py-1 font-semibold text-orange-600">Rp {{ number_format($openShift->opening_balance, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="mx-auto max-w-5xl p-4">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

            {{-- Product grid --}}
            <div class="lg:col-span-2">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800">Produk Tersedia</h2>
                    <span class="text-xs text-gray-400">{{ $products->count() }} produk</span>
                </div>

                {{-- Category filter pills --}}
                <div class="mb-3 flex flex-wrap gap-1.5">
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-[11px] font-semibold text-orange-700">Semua</span>
                    @foreach($categories as $cat)
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-[11px] font-medium text-gray-600">{{ $cat->name }}</span>
                    @endforeach
                </div>

                {{-- Products --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach($products as $product)
                    <div class="cursor-pointer rounded-xl border border-gray-200 bg-white p-3 transition-all hover:border-orange-300 hover:shadow-md hover:shadow-orange-100">
                        <div class="mb-2 flex aspect-square items-center justify-center rounded-lg bg-gradient-to-br from-orange-50 to-rose-50">
                            <i class="fas fa-box text-2xl text-orange-300"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-800 truncate">{{ $product->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ $product->sku }}</p>
                        <p class="mt-1 text-sm font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-400">Stok: {{ $product->stock }} {{ $product->unit }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cart panel --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-4 py-3">
                    <h3 class="text-sm font-bold text-gray-800">Keranjang</h3>
                </div>
                <div class="flex flex-col items-center justify-center p-6 text-center">
                    <i class="fas fa-shopping-cart text-4xl text-gray-200 mb-2"></i>
                    <p class="text-xs text-gray-400">Keranjang kosong</p>
                    <p class="text-[10px] text-gray-300 mt-1">Klik produk untuk menambahkan</p>
                </div>
                <div class="border-t border-gray-100 px-4 py-3">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Subtotal</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-bold text-gray-900">
                        <span>Total</span>
                        <span>Rp 0</span>
                    </div>
                    <button disabled
                        class="mt-3 w-full rounded-lg bg-gray-200 px-4 py-2.5 text-xs font-bold text-gray-400 cursor-not-allowed">
                        <i class="fas fa-lock mr-1"></i> Login untuk Bertransaksi
                    </button>
                    <p class="mt-2 text-center text-[10px] text-gray-400">
                        <a href="{{ route('login') }}" class="text-orange-500 hover:underline">Login</a> untuk mencoba POS lengkap
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 bg-white px-4 py-3 text-center text-[10px] text-gray-400">
        BaliPOS v{{ config('app.version', '1.0.0') }} &mdash; Demo Mode
    </div>
</body>
</html>
