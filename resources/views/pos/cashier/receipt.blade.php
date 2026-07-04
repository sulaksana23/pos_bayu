<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Struk {{ $transaction->invoice_no }} - {{ config('app.name', 'POS') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'JetBrains Mono', monospace;
            background: #f3f4f6;
            color: #111;
            margin: 0;
            padding: 16px;
            display: flex;
            justify-content: center;
        }
        .receipt {
            width: 58mm;
            background: white;
            padding: 8px 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .r {
            text-align: right;
        }
        .c {
            text-align: center;
        }
        .line {
            border-top: 1px dashed #444;
            margin: 6px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
            font-size: 11px;
        }
        .item-name {
            font-weight: 600;
        }
        .small {
            font-size: 10px;
        }
        .big {
            font-size: 14px;
            font-weight: 700;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt {
                box-shadow: none;
                padding: 4px 4px;
                width: 58mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="c big">{{ strtoupper(config('app.name', 'POS')) }}</div>
        <div class="c small">Jl. Contoh No. 123, Makassar</div>
        <div class="c small">Telp: 0812-3456-7890</div>
        <div class="line"></div>
        <div class="row small"><span>No</span><span>{{ $transaction->invoice_no }}</span></div>
        <div class="row small">
            <span>Tgl</span><span>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="row small">
            <span>Kasir</span><span>{{ $transaction->cashier?->name ?? '-' }}</span>
        </div>
        @if ($transaction->customer)
            <div class="row small">
                <span>Customer</span><span>{{ $transaction->customer->name }}</span>
            </div>
        @endif
        <div class="line"></div>

        @foreach ($transaction->items as $item)
            <div style="font-size: 11px; margin-bottom: 3px">
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="row small">
                    <span
                        >{{ $item->qty }} {{ $item->product_sku ? 'x ' . $item->product_sku : '' }} @ {{ number_format($item->price,0,',','.') }}</span
                    >
                    <span>{{ number_format($item->subtotal,0,',','.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="line"></div>
        <div class="row">
            <span>Subtotal</span><span>{{ number_format($transaction->subtotal,0,',','.') }}</span>
        </div>
        @if ($transaction->discount > 0)
            <div class="row">
                <span>Diskon</span
                ><span>-{{ number_format($transaction->discount,0,',','.') }}</span>
            </div>
        @endif
        @if ($transaction->tax > 0)
            <div class="row">
                <span>Pajak</span><span>{{ number_format($transaction->tax,0,',','.') }}</span>
            </div>
        @endif
        <div class="row big">
            <span>TOTAL</span><span>Rp {{ number_format($transaction->total,0,',','.') }}</span>
        </div>
        <div class="line"></div>
        <div class="row">
            <span>Bayar ({{ strtoupper($transaction->payment_method) }})</span
            ><span>{{ number_format($transaction->paid,0,',','.') }}</span>
        </div>
        @if ($transaction->change_amount > 0)
            <div class="row">
                <span>Kembali</span
                ><span>{{ number_format($transaction->change_amount,0,',','.') }}</span>
            </div>
        @endif

        <div class="line"></div>
        <div class="c small">Terima kasih atas kunjungannya!</div>
        <div class="c small">Simpan struk sebagai bukti pembayaran</div>
        @if ($transaction->notes)
            <div class="line"></div>
            <div class="c small">Catatan: {{ $transaction->notes }}</div>
        @endif
        <div class="line"></div>
        <div class="c small">— Powered by GHouse POS —</div>
    </div>

    <div
        class="no-print"
        style="
            position: fixed;
            bottom: 16px;
            left: 16px;
            right: 16px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        "
    >
        <button
            onclick="window.print()"
            class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-orange-600"
        >
            <i class="fas fa-print mr-1"></i> Print Lagi
        </button>
        <button
            onclick="closeAfterPrint()"
            class="rounded-lg bg-gray-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-gray-800"
        >
            <i class="fas fa-xmark mr-1"></i> Tutup
        </button>
        <a
            href="{{ route('pos.cashier.index') }}"
            class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-600"
        >
            <i class="fas fa-plus mr-1"></i> Transaksi Baru
        </a>
    </div>
    <script>
        const url = new URL(window.location.href);
        if (url.searchParams.get('autoprint')) {
            setTimeout(() => window.print(), 250);
        }
        function closeAfterPrint() {
            if (window.opener) {
                window.close();
            } else {
                window.location.href = '{{ route('pos.dashboard') }}';
            }
        }
    </script>
</body>
</html>
