@extends ('layouts.cashier', ['currentShift' => $openShift])
@section ('title','Kasir')

@section ('content')
<div
    x-data="posCashier()"
    x-init="init()"
    class="flex h-full flex-col"
>
    {{-- MOBILE TOP TAB BAR --}}
    <div class="no-print shrink-0 border-b border-gray-100 bg-white lg:hidden">
        <div class="flex">
            <button
                @click="mobileTab = 'products'"
                :class="mobileTab === 'products' ? 'text-orange-600 border-b-2 border-orange-500' : 'text-gray-400 border-b-2 border-transparent'"
                class="flex flex-1 items-center justify-center gap-1.5 py-2.5 text-xs font-semibold transition-colors"
            >
                <i class="fas fa-th-large text-xs"></i> Produk
            </button>
            <button
                @click="mobileTab = 'cart'"
                :class="mobileTab === 'cart' ? 'text-orange-600 border-b-2 border-orange-500' : 'text-gray-400 border-b-2 border-transparent'"
                class="relative flex flex-1 items-center justify-center gap-1.5 py-2.5 text-xs font-semibold transition-colors"
            >
                <span class="relative inline-block">
                    <i class="fas fa-shopping-cart text-xs"></i>
                    <span x-show="cart.length > 0" x-text="cart.length"
                        class="absolute -top-1.5 -right-2.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-orange-500 text-[8px] font-bold text-white"></span>
                </span>
                Keranjang
            </button>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="relative min-h-0 flex-1 lg:grid lg:grid-cols-5 lg:gap-3 lg:p-3">

        {{-- LEFT: Products --}}
        <div
            x-show="mobileTab === 'products'"
            x-cloak
            class="flex h-full flex-col overflow-hidden bg-white lg:!flex lg:col-span-3 lg:rounded-2xl lg:border lg:border-gray-100 lg:shadow-sm"
        >
            {{-- Search + Categories --}}
            <div class="border-b border-gray-100 bg-gray-50/80 p-3">
                <div class="relative">
                    <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-xs text-gray-400"></i>
                    <input
                        x-ref="searchInput"
                        x-model.debounce.200ms="query"
                        type="text"
                        placeholder="Scan barcode atau nama produk... (F2)"
                        class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pr-10 pl-9 text-sm shadow-sm transition focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                    />
                    <kbd class="absolute top-1/2 right-3 -translate-y-1/2 rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] text-gray-400">F2</kbd>
                </div>
                <div class="scroll-thin -mx-1 mt-2.5 flex items-center gap-1.5 overflow-x-auto px-1 pb-0.5">
                    <button
                        @click="activeCategory = '__all__'"
                        :class="activeCategory === '__all__' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-orange-300 hover:text-orange-600'"
                        class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all"
                    >
                        <i class="fas fa-th-large mr-1 text-[10px]"></i> Semua
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            @click="activeCategory='{{ $cat->id }}'"
                            :class="activeCategory == '{{ (string)$cat->id }}' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-orange-300 hover:text-orange-600'"
                            class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all"
                        >
                            <i class="fas {{ $cat->icon }} mr-1 text-[10px]"></i>{{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="scroll-thin flex-1 overflow-y-auto p-3">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 xl:grid-cols-4" x-ref="productGrid">
                    <template x-for="p in filteredProducts()" :key="p.id">
                        <button
                            @click="addToCart(p)"
                            :disabled="p.stock <= 0"
                            class="group flex flex-col rounded-xl border border-gray-100 bg-white p-2.5 text-left shadow-sm transition-all hover:border-orange-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-orange-400/30 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            <div class="mb-2 flex aspect-square w-full items-center justify-center overflow-hidden rounded-lg bg-gray-50">
                                <template x-if="p.image_url">
                                    <img :src="p.image_url" :alt="p.name" class="h-full w-full object-cover" loading="lazy" onerror="this.parentElement.innerHTML='<i class=\'fas fa-box text-gray-300 text-xl\'></i>'" />
                                </template>
                                <template x-if="!p.image_url">
                                    <i class="fas fa-box text-xl text-gray-300"></i>
                                </template>
                            </div>
                            <p class="line-clamp-2 flex-1 text-[11px] font-semibold leading-tight text-gray-800" x-text="p.name"></p>
                            <div class="mt-1.5 flex items-end justify-between gap-1">
                                <p class="text-sm font-bold text-orange-600" x-text="formatIDR(p.price)"></p>
                                <span
                                    class="shrink-0 rounded-md px-1.5 py-0.5 text-[10px] font-semibold"
                                    :class="p.stock <= 0 ? 'bg-red-100 text-red-600' : p.stock <= 5 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                    x-text="p.stock + ' ' + (p.unit || 'pcs')"
                                ></span>
                            </div>
                        </button>
                    </template>
                    <template x-if="filteredProducts().length === 0">
                        <div class="col-span-full py-16 text-center">
                            <i class="fas fa-search mb-3 text-4xl text-gray-200"></i>
                            <p class="text-sm text-gray-400">Produk tidak ditemukan</p>
                            <p class="mt-1 text-xs text-gray-300" x-show="query">untuk "<span x-text="query"></span>"</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart + Payment --}}
        <div
            x-show="mobileTab === 'cart'"
            x-cloak
            class="flex h-full flex-col overflow-hidden bg-white lg:!flex lg:col-span-2 lg:rounded-2xl lg:border lg:border-gray-100 lg:shadow-sm"
        >
            {{-- Cart Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-100">
                        <i class="fas fa-shopping-cart text-xs text-orange-600"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-800">Keranjang</span>
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500" x-text="cart.length + ' item'"></span>
                </div>
                <button @click="clearCart()" x-show="cart.length > 0"
                    class="rounded-lg px-2.5 py-1 text-xs font-semibold text-red-500 transition hover:bg-red-50">
                    <i class="fas fa-trash mr-1 text-[10px]"></i> Hapus
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="scroll-thin flex-1 overflow-y-auto px-3 py-2">
                <template x-for="(item, idx) in cart" :key="item.id + '_' + idx">
                    <div class="group mb-1.5 rounded-xl border border-transparent px-3 py-2.5 transition hover:border-orange-100 hover:bg-orange-50/30">
                        <div class="flex items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-1 text-xs font-semibold text-gray-800" x-text="item.name"></p>
                                <p class="mt-0.5 text-[11px] text-gray-400" x-text="formatIDR(item.price)"></p>
                            </div>
                            <button @click="removeFromCart(idx)"
                                class="mt-0.5 shrink-0 text-gray-300 transition hover:text-red-500">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center overflow-hidden rounded-lg border border-gray-200 bg-white">
                                <button @click="decrementQty(idx)" class="px-2.5 py-1.5 text-gray-500 transition hover:bg-orange-50 hover:text-orange-600">
                                    <i class="fas fa-minus text-[9px]"></i>
                                </button>
                                <input type="number" x-model.number="item.qty" @change="clampQty(idx)"
                                    min="0.01" step="0.01"
                                    class="w-12 bg-transparent text-center text-xs font-bold text-gray-800 focus:outline-none" />
                                <button @click="incrementQty(idx)" class="px-2.5 py-1.5 text-gray-500 transition hover:bg-orange-50 hover:text-orange-600">
                                    <i class="fas fa-plus text-[9px]"></i>
                                </button>
                            </div>
                            <p class="text-sm font-bold text-orange-600" x-text="formatIDR(item.qty * item.price)"></p>
                        </div>
                    </div>
                </template>
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100">
                            <i class="fas fa-basket-shopping text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-400">Keranjang kosong</p>
                        <p class="mt-0.5 text-xs text-gray-300">Klik produk untuk menambahkan</p>
                    </div>
                </template>
            </div>

            {{-- Summary + Payment --}}
            <div class="border-t border-gray-100 bg-gray-50/50 px-4 py-3 space-y-3">

                {{-- Subtotal/Diskon/Pajak/Total --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold text-gray-700" x-text="formatIDR(subtotal())"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Diskon</span>
                        <input type="number" x-model.number="globalDiscount" min="0" step="500"
                            class="w-28 rounded-lg border border-gray-200 bg-white px-2 py-1 text-right text-xs font-semibold focus:border-orange-400 focus:outline-none focus:ring-1 focus:ring-orange-400/20" />
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Pajak</span>
                        <input type="number" x-model.number="tax" min="0" step="500"
                            class="w-28 rounded-lg border border-gray-200 bg-white px-2 py-1 text-right text-xs font-semibold focus:border-orange-400 focus:outline-none focus:ring-1 focus:ring-orange-400/20" />
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-orange-500 px-3 py-2.5 text-white">
                        <span class="text-xs font-bold uppercase tracking-wide">Total</span>
                        <span class="text-base font-bold" x-text="formatIDR(total())"></span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div>
                    <p class="mb-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400">Metode Pembayaran</p>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button @click="paymentMethod = 'cash'"
                            :class="paymentMethod === 'cash' ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white border border-gray-200 text-gray-500 hover:border-emerald-400 hover:text-emerald-600'"
                            class="rounded-xl py-2.5 text-center text-[10px] font-bold transition-all">
                            <i class="fas fa-money-bill-wave mb-1 block text-sm"></i> Cash
                        </button>
                        <button @click="paymentMethod = 'qris'"
                            :class="paymentMethod === 'qris' ? 'bg-blue-500 text-white border-blue-500 shadow-sm' : 'bg-white border border-gray-200 text-gray-500 hover:border-blue-400 hover:text-blue-600'"
                            class="rounded-xl py-2.5 text-center text-[10px] font-bold transition-all">
                            <i class="fas fa-qrcode mb-1 block text-sm"></i> QRIS
                        </button>
                        <button @click="paymentMethod = 'transfer'"
                            :class="paymentMethod === 'transfer' ? 'bg-violet-500 text-white border-violet-500 shadow-sm' : 'bg-white border border-gray-200 text-gray-500 hover:border-violet-400 hover:text-violet-600'"
                            class="rounded-xl py-2.5 text-center text-[10px] font-bold transition-all">
                            <i class="fas fa-building-columns mb-1 block text-sm"></i> TF
                        </button>
                        <button @click="paymentMethod = 'wallet'"
                            :class="paymentMethod === 'wallet' ? 'bg-pink-500 text-white border-pink-500 shadow-sm' : 'bg-white border border-gray-200 text-gray-500 hover:border-pink-400 hover:text-pink-600'"
                            class="rounded-xl py-2.5 text-center text-[10px] font-bold transition-all">
                            <i class="fas fa-wallet mb-1 block text-sm"></i> Wallet
                        </button>
                    </div>
                </div>

                {{-- Cash Input --}}
                <div x-show="paymentMethod === 'cash'" class="space-y-2">
                    <input type="number" x-model.number="cashTendered" min="0" step="1000"
                        class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-2.5 text-right font-mono text-base font-bold text-gray-800 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                        placeholder="0" />
                    <div class="grid grid-cols-4 gap-1.5">
                        <button @click="cashTendered = total()"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 py-1.5 text-[10px] font-bold text-emerald-700 transition hover:bg-emerald-100">Pas</button>
                        <button @click="cashTendered = Math.ceil(total() / 10000) * 10000"
                            class="rounded-lg border border-gray-200 bg-white py-1.5 text-[10px] font-bold text-gray-600 transition hover:bg-gray-50">10k</button>
                        <button @click="cashTendered = Math.ceil(total() / 50000) * 50000"
                            class="rounded-lg border border-gray-200 bg-white py-1.5 text-[10px] font-bold text-gray-600 transition hover:bg-gray-50">50k</button>
                        <button @click="cashTendered = Math.ceil(total() / 100000) * 100000"
                            class="rounded-lg border border-gray-200 bg-white py-1.5 text-[10px] font-bold text-gray-600 transition hover:bg-gray-50">100k</button>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2">
                        <span class="text-xs font-semibold text-gray-600">Kembalian</span>
                        <span class="font-mono text-sm font-bold text-emerald-600"
                            x-text="formatIDR(Math.max(0, (cashTendered || 0) - total()))"></span>
                    </div>
                </div>

                {{-- Non-cash info --}}
                <div x-show="paymentMethod !== 'cash'"
                    class="rounded-xl border border-blue-100 bg-blue-50 px-3 py-2">
                    <p class="text-[11px] text-blue-700">Konfirmasi pembayaran dari customer, lalu proses.</p>
                </div>

                {{-- Checkout Button --}}
                <button @click="checkout()" :disabled="cart.length === 0 || loading"
                    class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition-all hover:shadow-xl hover:shadow-orange-500/40 disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-show="!loading"><i class="fas fa-check-circle mr-2"></i>Proses Transaksi</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function posCashier() {
        return {
            products: @json($products),
            categories: @json($categories),
            query: '',
            activeCategory: '__all__',
            cart: [],
            globalDiscount: 0,
            tax: 0,
            paymentMethod: 'cash',
            cashTendered: 0,
            loading: false,
            mobileTab: 'products',

            init() {
                window.addEventListener('keydown', this.handleKey.bind(this));
                this.$nextTick(() => this.$refs.searchInput?.focus());
            },

            handleKey(e) {
                if (e.key === 'F2') { e.preventDefault(); this.$refs.searchInput?.focus(); }
                if (e.key === 'F4') { e.preventDefault(); this.clearCart(); }
                if (e.key === 'Escape') { this.query = ''; this.$refs.searchInput?.focus(); }
                if (e.key === 'Enter' && e.target === this.$refs.searchInput && this.query.length >= 2) {
                    const first = this.filteredProducts()[0];
                    if (first) { this.addToCart(first); this.query = ''; }
                }
                if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); this.checkout(); }
            },

            filteredProducts() {
                const q = this.query.trim().toLowerCase();
                return this.products.filter(p => {
                    const matchCat = this.activeCategory === '__all__' || String(p.category_id) === String(this.activeCategory);
                    if (!matchCat) return false;
                    if (!q) return true;
                    return (p.name||'').toLowerCase().includes(q) || (p.sku||'').toLowerCase().includes(q) || (p.barcode||'').toLowerCase().includes(q);
                });
            },

            addToCart(p) {
                if (p.stock <= 0) return;
                const existing = this.cart.find(c => c.id === p.id);
                if (existing) {
                    if (existing.qty + 1 > p.stock) { this.toast('Stok tidak cukup', 'warning'); return; }
                    existing.qty += 1;
                } else {
                    this.cart.push({ id: p.id, name: p.name, sku: p.sku, barcode: p.barcode, price: parseFloat(p.price), qty: 1, unit: p.unit, stock: parseInt(p.stock || 0) });
                }
                if (window.innerWidth < 1024) this.mobileTab = 'cart';
            },

            incrementQty(idx) {
                const c = this.cart[idx];
                if (c.qty + 1 > c.stock) { this.toast('Stok tidak cukup', 'warning'); return; }
                c.qty = +(c.qty + 1).toFixed(2);
            },
            decrementQty(idx) {
                const c = this.cart[idx];
                c.qty = +(c.qty - 1).toFixed(2);
                if (c.qty <= 0) this.cart.splice(idx, 1);
            },
            clampQty(idx) {
                const c = this.cart[idx];
                if (!c.qty || c.qty <= 0) c.qty = 1;
                if (c.qty > c.stock) c.qty = c.stock;
            },
            removeFromCart(idx) { this.cart.splice(idx, 1); },
            clearCart() { this.cart = []; this.cashTendered = 0; this.query = ''; this.globalDiscount = 0; this.tax = 0; this.$refs.searchInput?.focus(); },

            subtotal() { return this.cart.reduce((a, c) => a + c.qty * c.price, 0); },
            total() { return Math.max(0, this.subtotal() - (this.globalDiscount || 0) + (this.tax || 0)); },
            formatIDR(n) { return 'Rp\u00a0' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0)); },

            async checkout() {
                if (!this.cart.length) return;
                if (this.paymentMethod === 'cash' && (this.cashTendered || 0) < this.total()) {
                    this.toast('Tunai kurang dari total', 'error'); return;
                }
                this.loading = true;
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    const body = new FormData();
                    this.cart.forEach((it, i) => {
                        body.append(`items[${i}][product_id]`, it.id);
                        body.append(`items[${i}][qty]`, it.qty);
                        body.append(`items[${i}][price]`, it.price);
                        body.append(`items[${i}][discount]`, 0);
                    });
                    body.append('discount', this.globalDiscount || 0);
                    body.append('tax', this.tax || 0);
                    body.append('paid', this.cashTendered || this.total());
                    body.append('payment_method', this.paymentMethod);
                    const res = await fetch('{{ route('pos.cashier.checkout') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                        body, credentials: 'same-origin',
                    });
                    const data = await res.json();
                    if (!res.ok || !data.ok) throw new Error(data.message || 'Gagal');
                    window.location.href = data.redirect_url + '?autoprint=1';
                } catch (e) {
                    this.toast(e.message || 'Transaksi gagal', 'error');
                    this.loading = false;
                }
            },

            toast(message, type = 'info') {
                const colors = { info: 'bg-blue-600', success: 'bg-emerald-600', warning: 'bg-amber-500', error: 'bg-red-600' };
                const el = document.createElement('div');
                el.className = `fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-xl ${colors[type]} transition-all`;
                el.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>${message}`;
                document.body.appendChild(el);
                setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 2500);
            },
        };
    }
</script>
@endpush
@endsection
