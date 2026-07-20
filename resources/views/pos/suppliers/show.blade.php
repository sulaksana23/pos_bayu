@php
    $isAdmin = auth()->user()?->isAdmin();
@endphp
@extends('layouts.app')
@section('title', $supplier->name)
@section('breadcrumb', $supplier->name)

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">{{ $supplier->name }}</h1>
            <p class="text-xs text-gray-400">
                {{ $supplier->company ?: '-' }}
                @if($supplier->is_active)
                    <span class="ml-2 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Aktif</span>
                @else
                    <span class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-600">Nonaktif</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.suppliers.edit', $supplier) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-pen"></i> Edit
            </a>
            @if($isAdmin)
            <form action="{{ route('pos.suppliers.destroy', $supplier) }}" method="POST"
                  onsubmit="return confirm('Hapus supplier ini?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total PO</p>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($supplier->purchase_orders_count) }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Kontak</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $supplier->phone ?: '-' }}</p>
        </div>
        <div class="rounded-2xl border border-purple-100 bg-purple-50 p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600">PIC</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $supplier->pic_name ?: '-' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">NPWP</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $supplier->tax_id ?: '-' }}</p>
        </div>
    </div>

    {{-- Detail --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <h3 class="mb-3 text-sm font-bold text-gray-900">Informasi Supplier</h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Perusahaan</dt>
                    <dd class="font-semibold text-gray-800">{{ $supplier->company ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Email</dt>
                    <dd class="font-semibold text-gray-800">{{ $supplier->email ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Telepon</dt>
                    <dd class="font-semibold text-gray-800">{{ $supplier->phone ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Alamat</dt>
                    <dd class="font-semibold text-gray-800 text-right max-w-[200px]">{{ $supplier->address ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">PIC</dt>
                    <dd class="font-semibold text-gray-800">{{ $supplier->pic_name ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">No. HP PIC</dt>
                    <dd class="font-semibold text-gray-800">{{ $supplier->pic_phone ?: '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">NPWP</dt>
                    <dd class="font-semibold text-gray-800">{{ $supplier->tax_id ?: '-' }}</dd>
                </div>
            </dl>
            @if($supplier->notes)
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="text-[10px] font-semibold text-gray-400 mb-1">Catatan</p>
                    <p class="text-xs text-gray-600">{{ $supplier->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Recent POs --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <h3 class="mb-3 text-sm font-bold text-gray-900">Purchase Order Terbaru</h3>
            @if($supplier->purchaseOrders->count())
                <div class="space-y-2">
                    @foreach($supplier->purchaseOrders as $po)
                    <a href="{{ route('pos.purchase-orders.show', $po) }}"
                       class="flex items-center justify-between rounded-lg border border-gray-100 p-2.5 hover:bg-gray-50 transition">
                        <div>
                            <p class="text-xs font-semibold text-gray-800">{{ $po->po_no }}</p>
                            <p class="text-[10px] text-gray-400">{{ $po->created_at->format('d/m/Y') }}</p>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-medium
                            {{ $po->status === 'received' ? 'bg-emerald-100 text-emerald-700' : ($po->status === 'ordered' ? 'bg-blue-100 text-blue-700' : ($po->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ ucfirst($po->status) }}
                        </span>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center py-6 text-center">
                    <i class="fas fa-file-invoice text-2xl text-gray-200 mb-2"></i>
                    <p class="text-xs text-gray-400">Belum ada Purchase Order</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
