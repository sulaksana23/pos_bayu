@php
    $canManage = auth()->user()?->isAdmin() || auth()->user()?->isManager();
@endphp
@extends('layouts.app')
@section('title', 'PO ' . $order->po_no)
@section('breadcrumb', 'Detail PO')

@section('content')
<div class="space-y-4">

    {{-- Action bar --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">{{ $order->po_no }}</h1>
            <p class="text-xs text-gray-400">Dibuat oleh {{ $order->user->name }} &middot; {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($order->status === 'pending' && $canManage)
                <form action="{{ route('pos.purchase-orders.mark-ordered', $order) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="rounded-xl bg-blue-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-blue-600 transition">
                        <i class="fas fa-check mr-1"></i> Tandai Dipesan
                    </button>
                </form>
                <a href="{{ route('pos.purchase-orders.edit', $order) }}"
                   class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                    <i class="fas fa-pen mr-1"></i> Edit
                </a>
            @endif
            @if($order->status === 'ordered' && $canManage)
                <form action="{{ route('pos.purchase-orders.mark-received', $order) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="rounded-xl bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-600 transition"
                            onclick="return confirm('Terima PO ini? Stok produk akan otomatis bertambah.')">
                        <i class="fas fa-boxes mr-1"></i> Terima PO
                    </button>
                </form>
            @endif
            <a href="{{ route('pos.purchase-orders.index') }}"
               class="rounded-xl border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Status badge --}}
    @php
        $statusBadge = match($order->status) {
            'pending'  => ['fb-badge-yellow', 'Pending'],
            'ordered'  => ['fb-badge-blue', 'Dipesan'],
            'received' => ['fb-badge-green', 'Diterima'],
            'cancelled' => ['fb-badge-red', 'Batal'],
            default    => ['fb-badge-gray', $order->status],
        };
    @endphp

    {{-- Content --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Supplier info --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Supplier</h2>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $order->supplier->name }}</p>
                        @if($order->supplier->company)
                            <p class="text-xs text-gray-400">{{ $order->supplier->company }}</p>
                        @endif
                        @if($order->supplier->phone)
                            <p class="text-xs text-gray-400"><i class="fas fa-phone mr-1"></i> {{ $order->supplier->phone }}</p>
                        @endif
                        @if($order->supplier->email)
                            <p class="text-xs text-gray-400"><i class="fas fa-envelope mr-1"></i> {{ $order->supplier->email }}</p>
                        @endif
                    </div>
                    <span class="fb-badge {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                </div>
            </div>

            {{-- Items --}}
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-4 py-3">
                    <h2 class="text-sm font-bold text-gray-900">Item Barang ({{ $order->items->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="fb-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="hidden sm:table-cell">SKU</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $item->product_name }}</p>
                                </td>
                                <td class="hidden sm:table-cell text-sm text-gray-500">{{ $item->product_sku ?: '-' }}</td>
                                <td class="text-right text-sm">{{ number_format($item->qty) }}</td>
                                <td class="text-right text-sm">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-right text-sm font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 px-4 py-3 space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Diskon</span>
                        <span class="font-semibold text-red-500">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($order->tax > 0)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Pajak</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-gray-100 pt-1">
                        <span class="text-sm font-bold text-gray-700">Total</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <h2 class="text-sm font-bold text-gray-900 mb-3">Informasi</h2>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="fb-badge {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tgl. Order</span>
                        <span class="font-semibold">{{ $order->order_date?->format('d/m/Y') ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Estimasi Diterima</span>
                        <span class="font-semibold">{{ $order->expected_date?->format('d/m/Y') ?: '-' }}</span>
                    </div>
                    @if($order->received_date)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tgl. Diterima</span>
                        <span class="font-semibold">{{ $order->received_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
                @if($order->notes)
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="text-[11px] font-semibold text-gray-500 mb-1">Catatan</p>
                    <p class="text-xs text-gray-700">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
