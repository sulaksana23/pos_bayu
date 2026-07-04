@extends ('layouts.cashier', ['currentShift' => $openShift])
@section ('title','Kasir')

@section ('content')
    <div
        x-data="posCashier(@js($categories->pluck('name','id')->toArray() + ['__all__' => 'Semua']))"
        x-init="init()"
        class="grid h-full grid-cols-1 gap-4 p-4 lg:grid-cols-5"
    >
        {{-- LEFT: Products --}}
        <div
            class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-3"
        >
            <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 p-4">
                <div class="relative">
                    <i
                        class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"
                    ></i>
                    <input
                        x-ref="searchInput"
                        x-model.debounce.250ms="query"
                        type="text"
                        placeholder="Scan barcode atau ketik nama / SKU (F2)"
                        class="focus:border-olsera-blue focus:ring-olsera-blue/20 w-full rounded-lg border border-gray-300 py-3 pr-10 pl-10 text-sm shadow-sm focus:ring-2"
                    />
                    <span
                        class="absolute top-1/2 right-3 -translate-y-1/2 font-mono text-[10px] text-gray-400"
                        >F2</span
                    >
                </div>
                <div
                    class="scroll-thin -mx-1 mt-3 flex items-center gap-2 overflow-x-auto px-1 pb-1"
                >
                    <button
                        @click="activeCategory = '__all__'"
                        :class="activeCategory === '__all__'
                            ? 'bg-olsera-blue text-white shadow-md'
                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                        class="shrink-0 rounded-lg px-4 py-2 text-xs font-semibold transition-all"
                    >
                        <i class="fas fa-th-large mr-1"></i> Semua
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            @click="activeCategory='{{ $cat->id }}'"
                            :class="activeCategory== '{{ (string)$cat->id }}' ? 'bg-olsera-blue text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                            style="{{ (string)$cat->id === (string)\App\Models\Category::active()->orderBy('sort_order')->first()?->id ? '' : '' }} accent-color: {{ $cat->color }}"
                            class="shrink-0 rounded-lg px-4 py-2 text-xs font-semibold transition-all"
                        >
                            <i
                                class="fas {{ $cat->icon }} mr-1"
                                :style="activeCategory=='{{ (string)$cat->id }}' ? '' : 'color: {{ $cat->color }}'"
                            ></i>
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="scroll-thin flex-1 overflow-y-auto p-3">
                <div
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4"
                    x-ref="productGrid"
                >
                    <template x-for="p in filteredProducts()" :key="p.id">
                        <button
                            @click="addToCart(p)"
                            :disabled="p.stock <= 0"
                            class="group hover:border-olsera-blue focus:ring-olsera-blue/30 flex min-h-[140px] flex-col rounded-lg border border-gray-300 bg-white p-3 text-left transition-all hover:scale-105 hover:shadow-md focus:ring-2 focus:outline-none disabled:opacity-40 disabled:hover:scale-100"
                        >
                            <div
                                class="mb-2 flex aspect-square items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-gray-50 to-gray-100"
                            >
                                <img
                                    :src="p.image_url"
                                    :alt="p.name"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    onerror="this.style.display = 'none'"
                                />
                            </div>
                            <p class="line-clamp-2 flex-1 text-xs leading-tight font-semibold text-gray-800" x-text="
                                    p.name
                                "></p>
                            <div class="mt-1.5 flex items-end justify-between">
                                <p class="text-olsera-blue text-sm font-bold" x-text="
                                        formatIDR(p.price)
                                    "></p>
                                <span
                                    class="rounded px-1.5 py-0.5 text-[10px] font-semibold"
                                    :class="p.stock <= 0
                                        ? 'bg-red-100 text-red-600'
                                        : p.stock <= 5
                                          ? 'bg-amber-100 text-amber-700'
                                          : 'bg-emerald-100 text-emerald-700'"
                                    x-text="p.stock + ' ' + p.unit"
                                ></span>
                            </div>
                        </button>
                    </template>
                    <template x-if="filteredProducts().length === 0">
                        <div class="col-span-full py-12 text-center text-sm text-gray-400">
                            <i class="fas fa-search mb-2 text-3xl text-gray-300"></i>
                            <p>Tidak ada produk<span x-show="query"> untuk '<span x-text="query"></span>'</span>.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart + Payment --}}
        <div class="flex min-h-0 flex-col gap-3 lg:col-span-2">
            <div
                class="flex min-h-0 flex-1 flex-col rounded-xl border border-gray-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white p-4"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="bg-olsera-blue flex h-9 w-9 items-center justify-center rounded-lg text-white shadow-sm"
                        >
                            <i class="fas fa-shopping-cart text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800">Keranjang</h3>
                        <span
                            class="rounded-full bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-500"
                            x-text="`${cart.length} item`"
                        ></span>
                    </div>
                    <button
                        @click="clearCart()"
                        x-show="cart.length > 0"
                        class="text-xs font-semibold text-red-600 hover:text-red-700"
                    >
                        Reset
                    </button>
                </div>
                <div class="scroll-thin flex-1 overflow-y-auto p-3">
                    <template x-for="(item, idx) in cart" :key="item.id + '_' + idx">
                        <div
                            class="group mb-2 rounded-lg border border-transparent px-3 py-2.5 transition-all hover:border-blue-200 hover:bg-blue-50/50"
                        >
                            <div class="flex items-start gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-1 text-sm font-semibold text-gray-800" x-text="
                                            item.name
                                        "></p>
                                    <p class="mt-0.5 text-[11px] text-gray-500" x-text="
                                            formatIDR(item.price)
                                        "></p>
                                </div>
                                <button
                                    @click="removeFromCart(idx)"
                                    class="text-gray-300 opacity-0 transition group-hover:opacity-100 hover:text-red-500"
                                    title="Hapus"
                                >
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <div
                                    class="flex items-center gap-1 overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm"
                                >
                                    <button
                                        @click="decrementQty(idx)"
                                        class="hover:text-olsera-blue px-2.5 py-1 text-gray-600 transition hover:bg-blue-50"
                                    >
                                        <i class="fas fa-minus text-[10px]"></i>
                                    </button>
                                    <input
                                        type="number"
                                        x-model.number="item.qty"
                                        @change="clampQty(idx)"
                                        min="0.01"
                                        step="0.01"
                                        class="w-14 bg-transparent text-center text-xs font-semibold focus:outline-none"
                                    />
                                    <button
                                        @click="incrementQty(idx)"
                                        class="hover:text-olsera-blue px-2.5 py-1 text-gray-600 transition hover:bg-blue-50"
                                    >
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <p class="text-olsera-blue text-sm font-bold" x-text="
                                        formatIDR(item.qty * item.price)
                                    "></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="cart.length === 0">
                        <div
                            class="flex h-full flex-col items-center justify-center py-12 text-center text-sm text-gray-400"
                        >
                            <i class="fas fa-basket-shopping mb-2 text-3xl text-gray-300"></i>
                            <p>Keranjang kosong</p>
                            <p class="text-xs">Scan barcode atau klik produk.</p>
                        </div>
                    </template>
                </div>
                <div
                    class="space-y-3 border-t border-gray-200 bg-gradient-to-b from-white to-blue-50/30 p-4"
                >
                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                        <div class="font-medium text-gray-600">Subtotal</div>
                        <div
                            class="text-right font-mono font-semibold"
                            x-text="formatIDR(subtotal())"
                        ></div>
                        <div class="font-medium text-gray-600">Diskon</div>
                        <input
                            type="number"
                            x-model.number="globalDiscount"
                            min="0"
                            step="500"
                            class="focus:border-olsera-blue focus:ring-olsera-blue/20 rounded-lg border border-gray-300 px-2 py-1.5 text-right font-mono text-xs shadow-sm focus:ring-2 focus:outline-none"
                        />
                        <div class="font-medium text-gray-600">Pajak</div>
                        <input
                            type="number"
                            x-model.number="tax"
                            min="0"
                            step="500"
                            class="focus:border-olsera-blue focus:ring-olsera-blue/20 rounded-lg border border-gray-300 px-2 py-1.5 text-right font-mono text-xs shadow-sm focus:ring-2 focus:outline-none"
                        />
                        <div
                            class="border-t-2 border-gray-300 pt-2 text-sm font-bold text-gray-800"
                        >
                            TOTAL
                        </div>
                        <div
                            class="text-olsera-blue border-t-2 border-gray-300 pt-2 text-right text-base font-bold"
                            x-text="formatIDR(total())"
                        ></div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-bold text-gray-700">Metode Pembayaran</p>
                        <div class="grid grid-cols-4 gap-2">
                            <button
                                @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash'
                                    ? 'bg-emerald-500 text-white shadow-md border-emerald-600'
                                    : 'bg-white border border-gray-300 text-gray-700 hover:border-emerald-500'"
                                class="rounded-lg py-2.5 text-xs font-semibold transition-all"
                            >
                                <i class="fas fa-money-bill mb-0.5 block text-base"></i> Cash
                            </button>
                            <button
                                @click="paymentMethod = 'qris'"
                                :class="paymentMethod === 'qris'
                                    ? 'bg-olsera-blue text-white shadow-md border-olsera-blue-dark'
                                    : 'bg-white border border-gray-300 text-gray-700 hover:border-olsera-blue'"
                                class="rounded-lg py-2.5 text-xs font-semibold transition-all"
                            >
                                <i class="fas fa-qrcode mb-0.5 block text-base"></i> QRIS
                            </button>
                            <button
                                @click="paymentMethod = 'transfer'"
                                :class="paymentMethod === 'transfer'
                                    ? 'bg-violet-500 text-white shadow-md border-violet-600'
                                    : 'bg-white border border-gray-300 text-gray-700 hover:border-violet-500'"
                                class="rounded-lg py-2.5 text-xs font-semibold transition-all"
                            >
                                <i class="fas fa-bank mb-0.5 block text-base"></i> Transfer
                            </button>
                            <button
                                @click="paymentMethod = 'wallet'"
                                :class="paymentMethod === 'wallet'
                                    ? 'bg-pink-500 text-white shadow-md border-pink-600'
                                    : 'bg-white border border-gray-300 text-gray-700 hover:border-pink-500'"
                                class="rounded-lg py-2.5 text-xs font-semibold transition-all"
                            >
                                <i class="fas fa-wallet mb-0.5 block text-base"></i> Wallet
                            </button>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'cash'" class="space-y-2">
                        <input
                            type="number"
                            x-model.number="cashTendered"
                            min="0"
                            step="1000"
                            class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 font-mono text-lg font-bold shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                            placeholder="Tunai diterima (Rp)"
                        />
                        <div class="grid grid-cols-4 gap-2">
                            <button
                                @click="cashTendered = total()"
                                class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                            >
                                Uang Pas
                            </button>
                            <button
                                @click="cashTendered = Math.ceil(total() / 10000) * 10000"
                                class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                            >
                                10k
                            </button>
                            <button
                                @click="cashTendered = Math.ceil(total() / 50000) * 50000"
                                class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                            >
                                50k
                            </button>
                            <button
                                @click="cashTendered = Math.ceil(total() / 100000) * 100000"
                                class="rounded-lg border border-emerald-200 bg-emerald-50 py-2 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100"
                            >
                                100k
                            </button>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-2 pt-1 text-sm"
                        >
                            <span class="font-semibold text-gray-700">Kembalian:</span>
                            <span
                                class="font-mono text-base font-bold text-emerald-600"
                                x-text="formatIDR(Math.max(0, (cashTendered || 0) - total()))"
                            ></span>
                        </div>
                    </div>

                    <div
                        x-show="paymentMethod !== 'cash'"
                        class="space-y-1.5 rounded-lg border border-blue-200 bg-blue-50 p-3"
                    >
                        <p class="text-[11px] text-gray-600">Konfirmasi pembayaran non-tunai dari sisi customer dulu lalu klik "Proses Transaksi".</p>
                        <button
                            @click="cashTendered = total()"
                            class="text-olsera-blue hover:text-olsera-blue-dark text-[11px] font-semibold"
                        >
                            Set bayar = total
                        </button>
                    </div>

                    <button
                        @click="checkout()"
                        :disabled="cart.length === 0 || loading"
                        class="from-olsera-blue to-olsera-blue-dark shadow-olsera-blue/30 w-full rounded-lg bg-gradient-to-r py-3.5 font-bold text-white shadow-lg transition-all hover:scale-105 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                    >
                        <i class="fas fa-check-circle mr-2"></i>
                        <span x-show="!loading">Proses Transaksi</span>
                        <span x-show="loading"
                            ><i class="fas fa-spinner fa-spin mr-1"></i> Memproses...</span
                        >
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push ('scripts')
        <script>
            function posCashier(categoryMap) {
                return {
                    products: @json ($products),
                    categories: @json ($categories),
                    query: '',
                    activeCategory: '__all__',
                    cart: [],
                    globalDiscount: 0,
                    tax: 0,
                    paymentMethod: 'cash',
                    cashTendered: 0,
                    loading: false,

                    init() {
                        window.addEventListener('keydown', this.handleKey.bind(this));
                        this.focusSearch();
                    },
                    focusSearch() {
                        this.$refs.searchInput?.focus();
                    },
                    handleKey(e) {
                        if (e.key === 'F2') {
                            e.preventDefault();
                            this.focusSearch();
                        }
                        if (e.key === 'F4') {
                            e.preventDefault();
                            this.clearCart();
                        }
                        if (e.key === 'Escape') {
                            this.query = '';
                            this.focusSearch();
                        }
                        if (
                            e.key === 'Enter' &&
                            e.target === this.$refs.searchInput &&
                            this.query.length >= 3
                        ) {
                            const first = this.filteredProducts()[0];
                            if (first) {
                                this.addToCart(first);
                                this.query = '';
                            }
                        }
                        if (e.ctrlKey && e.key === 'Enter') {
                            e.preventDefault();
                            this.checkout();
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
                                this.toast('Stok tidak cukup', 'warning');
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
                            this.toast('Stok tidak cukup', 'warning');
                            return;
                        }
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
                    removeFromCart(idx) {
                        this.cart.splice(idx, 1);
                    },
                    clearCart() {
                        this.cart = [];
                        this.cashTendered = 0;
                        this.query = '';
                        this.globalDiscount = 0;
                        this.tax = 0;
                        this.focusSearch();
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

                    async checkout() {
                        if (!this.cart.length) return;
                        if ((this.cashTendered || 0) < this.total() && this.paymentMethod === 'cash') {
                            this.toast('Tunai kurang dari total', 'error');
                            return;
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
                                body,
                                credentials: 'same-origin',
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
    @endpush
@endsection
