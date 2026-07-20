@php
    $isEditing = $order->exists;
@endphp
@extends('layouts.app')
@section('title', $isEditing ? 'Edit PO' : 'Buat PO')
@section('breadcrumb', $isEditing ? 'Edit PO' : 'Buat PO')

@section('content')
<div x-data="poForm({
    items: {{ $isEditing ? json_encode($order->items->map(fn($i) => [
        'product_id' => $i->product_id,
        'product_name' => $i->product_name,
        'product_sku' => $i->product_sku,
        'qty' => $i->qty,
        'price' => (float) $i->price,
        'subtotal' => (float) $i->subtotal,
    ])) : '[]' }}
})">
    <form action="{{ $isEditing ? route('pos.purchase-orders.update', $order) : route('pos.purchase-orders.store') }}"
          method="POST">
        @csrf
        @if($isEditing) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

            {{-- Left: Form --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- PO Info --}}
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <h2 class="text-sm font-bold text-gray-900 mb-3">Informasi PO</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                                <option value="">Pilih supplier...</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id', $order->supplier_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} {{ $s->company ? '('.$s->company.')' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tgl. Order</label>
                            <input type="date" name="order_date"
                                   value="{{ old('order_date', $order->order_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tgl. Estimasi Diterima</label>
                            <input type="date" name="expected_date"
                                   value="{{ old('expected_date', $order->expected_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900">Item Barang</h2>
                        <button type="button" @click="addItem()"
                                class="rounded-lg bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-600 hover:bg-orange-100 transition">
                            <i class="fas fa-plus mr-0.5"></i> Tambah
                        </button>
                    </div>

                    <template x-for="(item, i) in items" :key="i">
                        <div class="mb-2 rounded-xl border border-gray-100 bg-gray-50/50 p-3">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Nama Barang</label>
                                        <input type="text"
                                               :name="`items[${i}][product_name]`"
                                               x-model="item.product_name"
                                               class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">SKU</label>
                                        <input type="text"
                                               :name="`items[${i}][product_sku]`"
                                               x-model="item.product_sku"
                                               class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                                    </div>
                                </div>
                                <button type="button" @click="removeItem(i)"
                                        class="shrink-0 rounded-lg p-1 text-gray-300 hover:text-red-500 transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <input type="hidden" :name="`items[${i}][product_id]`" x-model="item.product_id">
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Qty</label>
                                    <input type="number" min="1"
                                           :name="`items[${i}][qty]`"
                                           x-model.number="item.qty"
                                           @input="calcItem(i)"
                                           class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Harga Satuan</label>
                                    <input type="number" min="0"
                                           :name="`items[${i}][price]`"
                                           x-model.number="item.price"
                                           @input="calcItem(i)"
                                           class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Subtotal</label>
                                    <p class="mt-1.5 text-xs font-bold text-gray-800" x-text="formatRp(item.subtotal)"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="items.length === 0">
                        <div class="py-6 text-center">
                            <i class="fas fa-cart-plus text-xl text-gray-200 mb-1"></i>
                            <p class="text-xs text-gray-400">Belum ada item. Klik "Tambah" untuk menambahkan barang.</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <h2 class="text-sm font-bold text-gray-900 mb-3">Ringkasan</h2>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-bold text-gray-800" x-text="formatRp(subtotal)">Rp 0</span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Diskon</label>
                            <input type="number" min="0" name="discount" x-model.number="discount" @input="calcTotal"
                                   class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Pajak</label>
                            <input type="number" min="0" name="tax" x-model.number="tax" @input="calcTotal"
                                   class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400">
                        </div>
                        <div class="border-t border-gray-100 pt-2">
                            <div class="flex justify-between text-sm">
                                <span class="font-bold text-gray-700">Total</span>
                                <span class="font-bold text-gray-900" x-text="formatRp(total)">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="2"
                                  class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:border-orange-400 focus:ring-1 focus:ring-orange-400">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <button type="submit"
                            class="mt-4 w-full rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Buat Purchase Order' }}
                    </button>

                    @if($isEditing && $order->status === 'pending')
                        <a href="{{ route('pos.purchase-orders.show', $order) }}"
                           class="mt-2 block w-full text-center rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                            Batal
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('poForm', (init) => ({
        items: init.items.length ? init.items : [],
        discount: {{ old('discount', $order->discount ?? 0) }},
        tax: {{ old('tax', $order->tax ?? 0) }},
        subtotal: 0,
        total: 0,

        init() {
            this.calcTotal();
        },

        addItem() {
            this.items.push({
                product_id: '',
                product_name: '',
                product_sku: '',
                qty: 1,
                price: 0,
                subtotal: 0,
            });
        },

        removeItem(i) {
            this.items.splice(i, 1);
            this.calcTotal();
        },

        calcItem(i) {
            this.items[i].subtotal = this.items[i].qty * this.items[i].price;
            this.calcTotal();
        },

        calcTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
            this.total = this.subtotal - (this.discount || 0) + (this.tax || 0);
        },

        formatRp(val) {
            const num = val || 0;
            return 'Rp ' + num.toLocaleString('id-ID');
        },
    }));
});
</script>
@endpush
