<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barcode - {{ $product->name }}</title>
    <style>
        @page {
            size: 58mm 40mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 40mm;
            background: white;
        }

        .barcode-container {
            width: 58mm;
            padding: 3mm;
            text-align: center;
        }

        .product-name {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .barcode-svg {
            width: 100%;
            height: 15mm;
            margin: 2mm 0;
        }

        .barcode-number {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 2mm 0;
        }

        .price {
            font-size: 12px;
            font-weight: bold;
            margin-top: 1mm;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            transition: all 0.2s;
        }

        .print-button:hover {
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Barcode
    </button>

    <div class="barcode-container">
        <div class="product-name">{{ Str::upper($product->name) }}</div>

        <!-- Barcode SVG using Code128 -->
        <svg class="barcode-svg" viewBox="0 0 200 60">
            <!-- Simple barcode representation -->
            @php
                $barcodeValue = $product->barcode ?? $product->sku;
                $bars = str_split(str_pad(dechex(crc32($barcodeValue)), 20, '0'));
            @endphp

            <rect x="0" y="0" width="200" height="60" fill="white" />

            @foreach ($bars as $index => $bar)
                @php
                    $height = (hexdec($bar) % 3 + 2) * 10;
                    $x = $index * 10;
                @endphp
                @if ($index % 2 == 0)
                    <rect
                        x="{{ $x }}"
                        y="{{ 30 - $height/2 }}"
                        width="8"
                        height="{{ $height }}"
                        fill="black"
                    />
                @endif
            @endforeach
        </svg>

        <div class="barcode-number">{{ $product->barcode ?? $product->sku }}</div>

        <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // Auto print on load (optional)
        // window.onload = () => setTimeout(() => window.print(), 500);
    </script>
</body>
</html>
