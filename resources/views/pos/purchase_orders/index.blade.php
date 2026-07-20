@php
    $isAdmin = auth()->user()?->isAdmin();
@endphp
@extends('layouts.app')
@section('title', 'Purchase Order')
@section('breadcrumb', 'Purchase Order')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-bold text-gray-900">Purchase Order</h1>
            <p class="text-xs text-gray-400">Pemesanan barang ke supplier</p>
        </div>
        <a href="{{ route('pos.purchase-orders.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-500 px-3.5 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/30 transition-all hover:shadow-lg">
            <i class="fas fa-plus"></i> PO Baru
        </a>
    </div>

    {{-- Stats --}}
    @php
        $stats = [
            'pending' => ['label' => 'Pending', 'color' => 'yellow'],
            'ordered' => ['label' => 'Dipesan', 'color' => 'blue'],
            'received' => ['label' => 'Diterima', 'color' => 'green'],
        ];
        $counts = ['pending' => 0, 'ordered' => 0, 'received' => 0];
        foreach (\App\Models\PurchaseOrder::selectRaw("status, count(*) as total")->groupBy('status')->get() as $s) {
            $counts[$s->status] = $s->total;
        }
    @endphp
    <div class="grid grid-cols-3 gap-3">
        @foreach($stats as $key => $st)
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm text-center">
            <p class="text-[10px] font-bold tracking-wider uppercase text-gray-400">{{ $st['label'] }}</p>
            <p class="mt-0.5 text-xl font-bold text-gray-900">{{ $counts[$key] ?? 0 }}</p>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="fb-table">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th class="hidden sm:table-cell">Supplier</th>
                        <th class="hidden md:table-cell">Items</th>
                        <th>Total</th>
                        <th class="hidden lg:table-cell">Tgl. Order</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $po)
                    @php
                        $statusBadge = match($po->status) {
                            'pending'  => 'fb-badge-yellow',
                            'ordered'  => 'fb-badge-blue',
                            'received' => 'fb-badge-green',
                            'cancelled' => 'fb-badge-red',
                            default    => 'fb-badge-gray',
                        };
                        $statusLabel = match($po->status) {
                            'pending'  => 'Pending',
                            'ordered'  => 'Dipesan',
                            'received' => 'Diterima',
                            'cancelled' => 'Batal',
                            default    => ucfirst($po->status),
                        };
                    @endphp
                    <tr class="cursor-pointer" onclick="window.location='{{ route('pos.purchase-orders.show', $po) }}'">
                        <td>
                            <p class="font-semibold text-gray-900 text-sm">{{ $po->po_no }}</p>
                            <p class="text-[11px] text-gray-400">{{ $po->created_at->format('d/m/Y') }}</p>
                        </td>
                        <td class="hidden sm:table-cell">
                            <span class="text-sm text-gray-700">{{ $po->supplier?->name ?? '-' }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-sm text-gray-700">{{ $po->items->count() }} item</span>
                        </td>
                        <td>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($po->total, 0, ',', '.') }}</span>
                        </td>
                        <td class="hidden lg:table-cell">
                            <span class="text-sm text-gray-700">{{ $po->order_date ? $po->order_date->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td>
                            <span class="fb-badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('pos.purchase-orders.show', $po) }}"
                               class="rounded-lg p-1.5 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition inline-block">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <i class="fas fa-file-invoice text-3xl text-gray-200 mb-2"></i>
                                <p class="text-sm text-gray-400">Belum ada purchase order</p>
                                <a href="{{ route('pos.purchase-orders.create') }}"
                                   class="mt-2 text-xs font-semibold text-orange-500 hover:underline">
                                    Buat PO baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
