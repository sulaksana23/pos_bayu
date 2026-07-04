<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <meta name="csrf-token" content="demo-token" />
    <title>POS Demo - DevStack Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        dark: '#0f172a',
                        'olsera-blue': '#0ea5e9',
                        'olsera-blue-dark': '#0284c7',
                    },
                },
            },
        };
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        * {
            font-family: 'Inter', sans-serif;
        }
        .scroll-thin::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .scroll-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scroll-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .scroll-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        * {
            -webkit-tap-highlight-color: transparent;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slideUp 0.2s ease-out;
        }
        [x-cloak] {
            display: none !important;
        }

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area,
            .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="h-full bg-gray-50 font-sans text-gray-900 antialiased">
    <!-- Navbar identik dengan layout cashier asli -->
    <nav class="no-print fixed top-0 right-0 left-0 z-50 border-b border-gray-200 bg-white shadow-sm">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Left: Logo & Demo Badge -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-md">
                            <i class="fas fa-cash-register text-lg text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">GHouse POS</h1>
                            <p class="text-xs text-gray-500">Kasir</p>
                        </div>
                    </div>
                    <div class="hidden items-center gap-3 sm:flex">
                        <div class="h-5 w-px bg-gray-200"></div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
                            <i class="fas fa-play-circle text-amber-500"></i> Mode Demo
                        </span>
                        <div class="flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 ring-1 ring-green-200">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span>
                            Shift Aktif
                        </div>
                        <div class="hidden items-center gap-1.5 text-xs text-gray-600 lg:flex">
                            <i class="fas fa-clock text-gray-400"></i>
                            <span id="currentTime">00:00:00</span>
                        </div>
                    </div>
                </div>
                <!-- Right: Actions -->
                <div class="flex items-center gap-2">
                    <a
                        href="https://balitechsolution.com"
                        class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 transition hover:bg-gray-50"
                    >
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span class="hidden sm:inline">Kembali</span>
                    </a>
                    <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-xs font-bold text-white">D</div>
                        <div class="hidden text-left sm:block">
                            <p class="text-xs font-semibold text-gray-900">Demo User</p>
                            <p class="text-[10px] text-gray-500">Kasir</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div
        x-data="posCashier()"
        x-init="init()"
        class="h-screen overflow-hidden pt-[73px]"
    >
    <div class="grid h-full grid-cols-1 gap-4 p-4 lg:grid-cols-5">
        <!-- Left: Products -->
        <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-3">
            <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 p-4">
                <div class="relative">
                    <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                    <input
                        x-ref="searchInput"
                        x-model.debounce.250ms="query"
                        type="text"
                        placeholder="Scan barcode atau ketik nama / SKU (F2)"
                        class="focus:border-olsera-blue focus:ring-olsera-blue/20 w-full rounded-lg border border-gray-300 py-3 pr-10 pl-10 text-sm shadow-sm focus:ring-2"
                    />
                    <span class="absolute top-1/2 right-3 -translate-y-1/2 font-mono text-[10px] text-gray-400">F2</span>
                </div>
                <div class="scroll-thin -mx-1 mt-3 flex items-center gap-2 overflow-x-auto px-1 pb-1">
                    <button
                        @click="activeCategory = '__all__'"
                        :class="activeCategory === '__all__' ? 'bg-olsera-blue text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                        class="shrink-0 rounded-lg px-4 py-2 text-xs font-semibold transition-all"
                    >
                        <i class="fas fa-th-large mr-1"></i> Semua
                    </button>
                    <template x-for="cat in demoCategories" :key="cat.id">
                        <button
                            @click="activeCategory = cat.id"
                            :class="activeCategory == cat.id ? 'bg-olsera-blue text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                            class="shrink-0 rounded-lg px-4 py-2 text-xs font-semibold transition-all"
                        >
                            <i class="fas mr-1" :class="cat.icon" :style="activeCategory == cat.id ? '' : 'color:' + cat.color"></i>
                            <span x-text="cat.name"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="scroll-thin flex-1 overflow-y-auto p-3">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4" x-ref="productGrid">
                    <template x-for="p in filteredProducts()" :key="p.id">
                        <button
                            @click="addToCart(p)"
                            :disabled="p.stock <= 0"
                            class="group hover:border-olsera-blue focus:ring-olsera-blue/30 flex min-h-[140px] flex-col rounded-lg border border-gray-300 bg-white p-3 text-left transition-all hover:scale-105 hover:shadow-md focus:ring-2 focus:outline-none disabled:opacity-40 disabled:hover:scale-100"
                        >
                            <div class="mb-2 flex aspect-square items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-gray-50 to-gray-100">
                                <img
                                    :src="p.image_url"
                                    :alt="p.name"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    onerror="this.style.display='none'"
                                />
                            </div>
                            <p class="line-clamp-2 flex-1 text-xs leading-tight font-semibold text-gray-800" x-text="p.name"></p>
                            <div class="mt-1.5 flex items-end justify-between">
                                <p class="text-olsera-blue text-sm font-bold" x-text="formatIDR(p.price)"></p>
                                <span
                                    class="rounded px-1.5 py-0.5 text-[10px] font-semibold"
                                    :class="p.stock <= 0 ? 'bg-red-100 text-red-600' : p.stock <= 5 ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'"
                                    x-text="p.stock + ' ' + p.unit"
                                ></span>
                            </div>
                        </button>
                    </template>
                    <template x-if="filteredProducts().length === 0">
                        <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                            <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                <i class="fas fa-search text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-600">Tidak ada produk</p>
                            <p class="mt-1 text-xs text-slate-400" x-show="
                                    query
                                ">untuk '<span x-text="query"></span>'</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right: Cart -->
        <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <!-- Cart Header -->
            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-gray-600"></i>
                        <span class="font-semibold text-gray-900">Keranjang</span>
                        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-600" x-text="cart.length"></span>
                    </div>
                    <button
                        @click="clearCart()"
                        x-show="cart.length > 0"
                        class="rounded px-2 py-1 text-xs font-medium text-red-500 transition hover:bg-red-50 hover:text-red-700"
                    >
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="scroll-thin flex-1 overflow-y-auto p-3">
                <template x-for="(item, idx) in cart" :key="item.id + '_' + idx">
                    <div class="animate-slide-up group mb-2 rounded-lg border border-gray-200 bg-white p-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="mb-2 flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-1 text-sm font-semibold text-gray-800" x-text="item.name"></p>
                                <p class="text-olsera-blue text-xs font-medium" x-text="formatIDR(item.price)"></p>
                            </div>
                            <button
                                @click="removeFromCart(idx)"
                                class="shrink-0 rounded p-1 text-gray-300 opacity-0 transition group-hover:opacity-100 hover:bg-red-50 hover:text-red-500"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                <button @click="decrementQty(idx)" class="px-2.5 py-1.5 text-gray-500 transition hover:bg-gray-100">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input
                                    type="number"
                                    x-model.number="item.qty"
                                    @change="clampQty(idx)"
                                    min="1"
                                    class="w-10 border-none bg-transparent text-center text-sm font-bold text-gray-800 focus:outline-none"
                                />
                                <button @click="incrementQty(idx)" class="px-2.5 py-1.5 text-gray-500 transition hover:bg-gray-100">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <span class="text-sm font-bold text-gray-900" x-text="formatIDR(item.qty * item.price)"></span>
                        </div>
                    </div>
                </template>
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i class="fas fa-shopping-cart text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Keranjang Kosong</p>
                        <p class="mt-1 text-xs text-gray-400">Pilih produk untuk memulai</p>
                    </div>
                </template>
            </div>

            <!-- Cart Summary -->
            <div class="space-y-3 border-t border-gray-200 bg-white p-4">
                <!-- Subtotal, Diskon, Tax -->
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium" x-text="formatIDR(subtotal())"></span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Diskon</span>
                        <input
                            type="number"
                            x-model.number="globalDiscount"
                            min="0"
                            step="1000"
                            class="w-28 rounded border border-gray-300 bg-gray-50 px-2 py-1 text-right text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Pajak</span>
                        <input
                            type="number"
                            x-model.number="tax"
                            min="0"
                            step="1000"
                            class="w-28 rounded border border-gray-300 bg-gray-50 px-2 py-1 text-right text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Total -->
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                    <span class="text-sm font-bold text-gray-700">Total</span>
                    <span class="text-olsera-blue font-mono text-2xl font-bold" x-text="formatIDR(total())"></span>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="mb-2 block text-xs font-semibold tracking-wide text-gray-700 uppercase">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button
                            @click="paymentMethod = 'cash'"
                            :class="paymentMethod === 'cash' ? 'bg-emerald-500 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                            class="rounded-lg py-2.5 text-xs font-semibold transition"
                        >
                            <i class="fas fa-money-bill mb-1 block text-lg"></i>
                            Cash
                        </button>
                        <button
                            @click="paymentMethod = 'qris'"
                            :class="paymentMethod === 'qris' ? 'bg-blue-500 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                            class="rounded-lg py-2.5 text-xs font-semibold transition"
                        >
                            <i class="fas fa-qrcode mb-1 block text-lg"></i>
                            QRIS
                        </button>
                        <button
                            @click="paymentMethod = 'transfer'"
                            :class="paymentMethod === 'transfer' ? 'bg-violet-500 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                            class="rounded-lg py-2.5 text-xs font-semibold transition"
                        >
                            <i class="fas fa-bank mb-1 block text-lg"></i>
                            Bank
                        </button>
                        <button
                            @click="paymentMethod = 'wallet'"
                            :class="paymentMethod === 'wallet' ? 'bg-pink-500 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                            class="rounded-lg py-2.5 text-xs font-semibold transition"
                        >
                            <i class="fas fa-wallet mb-1 block text-lg"></i>
                            Wallet
                        </button>
                    </div>
                </div>

                <!-- Cash Input -->
                <div x-show="paymentMethod === 'cash'" class="space-y-2">
                    <input
                        type="number"
                        x-model.number="cashTendered"
                        min="0"
                        step="1000"
                        placeholder="Tunai diterima (Rp)"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-lg font-bold shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                    />
                    <div class="grid grid-cols-4 gap-2">
                        <button
                            @click="cashTendered = total()"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                        >Uang Pas</button>
                        <button
                            @click="cashTendered = Math.ceil(total() / 10000) * 10000"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                        >10k</button>
                        <button
                            @click="cashTendered = Math.ceil(total() / 50000) * 50000"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                        >50k</button>
                        <button
                            @click="cashTendered = Math.ceil(total() / 100000) * 100000"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                        >100k</button>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-2 text-sm">
                        <span class="font-semibold text-gray-700">Kembalian:</span>
                        <span class="font-mono text-base font-bold text-emerald-600" x-text="formatIDR(Math.max(0, (cashTendered || 0) - total()))"></span>
                    </div>
                </div>

                <div x-show="paymentMethod !== 'cash'" class="space-y-1.5 rounded-lg border border-blue-200 bg-blue-50 p-3">
                    <p class="text-[11px] text-gray-600">Konfirmasi pembayaran non-tunai dari sisi customer dulu lalu klik "Proses Transaksi".</p>
                    <button @click="cashTendered = total()" class="text-olsera-blue hover:text-olsera-blue-dark text-[11px] font-semibold">
                        Set bayar = total
                    </button>
                </div>

                <!-- Checkout Button -->
                <button
                    @click="checkoutDemo()"
                    :disabled="cart.length === 0 || loading"
                    class="from-olsera-blue to-olsera-blue-dark shadow-olsera-blue/30 w-full rounded-lg bg-gradient-to-r py-3.5 font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <i class="fas fa-bolt"></i>
                        Proses Transaksi
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div
        x-show="showReceipt"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeReceipt()"
    >
        <div
            x-show="showReceipt"
            x-cloak
            x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white shadow-2xl"
        >
            <!-- Receipt Content -->
            <div class="print-area p-6">
                <!-- Header -->
                <div class="mb-6 border-b-2 border-dashed border-slate-300 pb-6 text-center">
                    <div
                        class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600"
                    >
                        <i class="fas fa-cash-register text-2xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">GHouse POS</h2>
                    <p class="mt-1 text-sm text-gray-600">Jl. Contoh No. 123, Denpasar</p>
                    <p class="text-sm text-gray-600">Telp: (0361) 1234-5678</p>
                </div>

                <!-- Invoice Info -->
                <div class="mb-6 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">No. Invoice:</span>
                        <span
                            class="font-bold text-slate-900"
                            x-text="receiptData?.invoiceNo"
                        ></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tanggal:</span>
                        <span class="text-slate-900" x-text="receiptData?.date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Kasir:</span>
                        <span class="text-slate-900">Demo Mode</span>
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-6 border-t-2 border-b-2 border-dashed border-slate-300 py-4">
                    <template x-for="item in receiptData?.items" :key="item.id">
                        <div class="mb-3 last:mb-0">
                            <div class="mb-1 flex items-start justify-between">
                                <span
                                    class="flex-1 font-medium text-slate-900"
                                    x-text="item.name"
                                ></span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>
                                    <span x-text="item.qty"></span> x
                                    <span x-text="formatIDR(item.price)"></span>
                                </span>
                                <span
                                    class="font-semibold text-slate-900"
                                    x-text="formatIDR(item.qty * item.price)"
                                ></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Summary -->
                <div class="mb-6 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal:</span>
                        <span
                            class="text-slate-900"
                            x-text="formatIDR(receiptData?.subtotal)"
                        ></span>
                    </div>
                    <div x-show="receiptData?.discount > 0" class="flex justify-between text-sm">
                        <span class="text-slate-600">Diskon:</span>
                        <span
                            class="text-red-600"
                            x-text="'-' + formatIDR(receiptData?.discount)"
                        ></span>
                    </div>
                    <div x-show="receiptData?.tax > 0" class="flex justify-between text-sm">
                        <span class="text-slate-600">Pajak:</span>
                        <span class="text-slate-900" x-text="formatIDR(receiptData?.tax)"></span>
                    </div>
                    <div class="flex justify-between border-t-2 border-slate-300 pt-3">
                        <span class="text-lg font-bold text-slate-900">TOTAL:</span>
                        <span
                            class="text-lg font-bold text-sky-600"
                            x-text="formatIDR(receiptData?.total)"
                        ></span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="mb-6 space-y-2 rounded-lg bg-slate-50 p-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Metode Pembayaran:</span>
                        <span
                            class="font-semibold text-slate-900 uppercase"
                            x-text="receiptData?.paymentMethod"
                        ></span>
                    </div>
                    <div x-show="receiptData?.paymentMethod === 'cash'">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Tunai:</span>
                            <span
                                class="text-slate-900"
                                x-text="formatIDR(receiptData?.paid)"
                            ></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Kembalian:</span>
                            <span
                                class="font-bold text-emerald-600"
                                x-text="formatIDR(receiptData?.change)"
                            ></span>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="mb-6 flex justify-center">
                    <div class="rounded-lg border-2 border-slate-200 bg-white p-3">
                        <div
                            class="flex h-32 w-32 items-center justify-center rounded bg-slate-100"
                            x-html="generateQR(receiptData?.invoiceNo)"
                        ></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="space-y-2 border-t border-slate-200 pt-4 text-center">
                    <p class="text-sm font-semibold text-slate-900">Terima kasih atas kunjungan Anda!</p>
                    <p class="text-xs text-slate-500">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
                    <p class="mt-3 text-xs text-slate-400">Demo Mode - Transaksi tidak tersimpan</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="no-print flex gap-3 rounded-b-2xl border-t border-gray-200 bg-gray-50 p-4">
                <button
                    @click="printReceipt()"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white py-3 font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <button
                    @click="closeReceipt()"
                    class="from-olsera-blue to-olsera-blue-dark flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r py-3 font-bold text-white transition hover:opacity-90"
                >
                    <i class="fas fa-check"></i>
                    <span>Transaksi Baru</span>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        function posCashier() {
            return {
                demoCategories: [
                    { id: '1', name: 'Makanan', icon: 'fa-utensils', color: '#f59e0b' },
                    { id: '2', name: 'Minuman', icon: 'fa-coffee', color: '#0ea5e9' },
                    { id: '3', name: 'Snack', icon: 'fa-cookie-bite', color: '#8b5cf6' },
                    { id: '4', name: 'Lainnya', icon: 'fa-box', color: '#6b7280' },
                ],
                products: [
                    { id: 1, name: 'Nasi Goreng Spesial', price: 25000, stock: 50, unit: 'porsi', category_id: '1', image_url: '' },
                    { id: 2, name: 'Mie Ayam Bakso', price: 20000, stock: 30, unit: 'porsi', category_id: '1', image_url: '' },
                    { id: 3, name: 'Ayam Bakar', price: 35000, stock: 20, unit: 'porsi', category_id: '1', image_url: '' },
                    { id: 4, name: 'Soto Ayam', price: 18000, stock: 40, unit: 'porsi', category_id: '1', image_url: '' },
                    { id: 5, name: 'Es Teh Manis', price: 5000, stock: 100, unit: 'gelas', category_id: '2', image_url: '' },
                    { id: 6, name: 'Es Jeruk', price: 7000, stock: 80, unit: 'gelas', category_id: '2', image_url: '' },
                    { id: 7, name: 'Jus Alpukat', price: 15000, stock: 25, unit: 'gelas', category_id: '2', image_url: '' },
                    { id: 8, name: 'Kopi Hitam', price: 8000, stock: 60, unit: 'gelas', category_id: '2', image_url: '' },
                    { id: 9, name: 'Keripik Singkong', price: 10000, stock: 45, unit: 'bungkus', category_id: '3', image_url: '' },
                    { id: 10, name: 'Pisang Goreng', price: 12000, stock: 35, unit: 'porsi', category_id: '3', image_url: '' },
                    { id: 11, name: 'Bakwan Jagung', price: 8000, stock: 0, unit: 'porsi', category_id: '3', image_url: '' },
                    { id: 12, name: 'Air Mineral', price: 3000, stock: 200, unit: 'botol', category_id: '4', image_url: '' },
                ],
                query: '',
                activeCategory: '__all__',
                cart: [],
                globalDiscount: 0,
                tax: 0,
                paymentMethod: 'cash',
                cashTendered: 0,
                loading: false,
                showReceipt: false,
                receiptData: null,
                currentInvoiceNo: '',

                init() {
                    window.addEventListener('keydown', this.handleKey.bind(this));
                    this.generateInvoiceNo();
                },
                generateInvoiceNo() {
                    this.currentInvoiceNo = 'INV-DEMO-' + Date.now().toString().slice(-8);
                },
                handleKey(e) {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        this.$refs.searchInput?.focus();
                    }
                    if (e.key === 'Escape') {
                        this.query = '';
                    }
                },

                filteredProducts() {
                    const q = this.query.trim().toLowerCase();
                    return this.products.filter(p => {
                        const matchCat =
                            this.activeCategory === '__all__' ||
                            String(p.category_id) === String(this.activeCategory);
                        if (!matchCat) return false;
                        if (!q) return true;
                        return (
                            (p.name || '').toLowerCase().includes(q) ||
                            (p.sku || '').toLowerCase().includes(q) ||
                            (p.barcode || '').toLowerCase().includes(q)
                        );
                    });
                },

                addToCart(p) {
                    if (p.stock <= 0) return;
                    const existing = this.cart.find(c => c.id === p.id);
                    if (existing) {
                        if (existing.qty + 1 > p.stock) {
                            this.toast('Stok tidak cukup', 'error');
                            return;
                        }
                        existing.qty += 1;
                    } else {
                        this.cart.push({
                            id: p.id,
                            name: p.name,
                            sku: p.sku,
                            barcode: p.barcode,
                            price: parseFloat(p.price),
                            qty: 1,
                            unit: p.unit,
                            stock: parseInt(p.stock || 0),
                        });
                    }
                },
                incrementQty(idx) {
                    const c = this.cart[idx];
                    if (c.qty + 1 > c.stock) {
                        this.toast('Stok tidak cukup', 'error');
                        return;
                    }
                    c.qty += 1;
                },
                decrementQty(idx) {
                    const c = this.cart[idx];
                    c.qty -= 1;
                    if (c.qty <= 0) this.cart.splice(idx, 1);
                },
                clampQty(idx) {
                    const c = this.cart[idx];
                    if (!c.qty || c.qty <= 0) c.qty = 1;
                    if (c.qty > c.stock) c.qty = c.stock;
                },
                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
                },
                clearCart() {
                    this.cart = [];
                    this.cashTendered = 0;
                    this.query = '';
                    this.globalDiscount = 0;
                    this.tax = 0;
                },

                subtotal() {
                    return this.cart.reduce((a, c) => a + c.qty * c.price, 0);
                },
                total() {
                    return Math.max(0, this.subtotal() - (this.globalDiscount || 0) + (this.tax || 0));
                },

                formatIDR(n) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
                },

                checkoutDemo() {
                    if (!this.cart.length) return;
                    if ((this.cashTendered || 0) < this.total() && this.paymentMethod === 'cash') {
                        this.toast('Tunai kurang dari total', 'error');
                        return;
                    }
                    this.loading = true;

                    // Generate receipt data using current invoice number
                    this.receiptData = {
                        invoiceNo: this.currentInvoiceNo,
                        date: new Date().toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                        }),
                        items: [...this.cart],
                        subtotal: this.subtotal(),
                        discount: this.globalDiscount,
                        tax: this.tax,
                        total: this.total(),
                        paid: this.paymentMethod === 'cash' ? this.cashTendered : this.total(),
                        change:
                            this.paymentMethod === 'cash'
                                ? Math.max(0, this.cashTendered - this.total())
                                : 0,
                        paymentMethod: this.paymentMethod,
                    };

                    setTimeout(() => {
                        this.loading = false;
                        this.toast('✓ Transaksi berhasil!', 'success');
                        // Clear cart immediately and show receipt
                        this.cart = [];
                        this.cashTendered = 0;
                        this.globalDiscount = 0;
                        this.tax = 0;
                        this.showReceipt = true;
                        // Generate new invoice number for next transaction
                        this.generateInvoiceNo();
                    }, 600);
                },
                closeReceipt() {
                    this.showReceipt = false;
                    this.receiptData = null;
                },
                printReceipt() {
                    window.print();
                },
                generateQR(text) {
                    if (!text) return '';
                    const size = 128;
                    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(text)}`;
                    return `<img src="${qrUrl}" alt="QR Code" class="w-full h-full" />`;
                },
                toast(message, type = 'info') {
                    const colors = {
                        info: 'bg-blue-50 text-blue-800 border-blue-200',
                        success: 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        warning: 'bg-amber-50 text-amber-800 border-amber-200',
                        error: 'bg-red-50 text-red-800 border-red-200',
                    };
                    const el = document.createElement('div');
                    el.className = `fixed bottom-4 right-4 px-4 py-3 rounded-xl border ${colors[type]} shadow-xl text-sm font-medium z-50 animate-bounce`;
                    el.textContent = message;
                    document.body.appendChild(el);
                    setTimeout(() => el.remove(), 3000);
                },
            };
        }
    </script>

    <script>
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
            const timeEl = document.getElementById('currentTime');
            if (timeEl) timeEl.textContent = timeStr;
        }, 1000);
    </script>
</body>
</html>
</body>
</html>
