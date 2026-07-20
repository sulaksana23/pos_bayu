<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barcode - {{ $product->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        @page { size: 58mm 40mm; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            display: flex; align-items: center; justify-content: center;
            min-height: 40mm; background: white;
        }
        .barcode-container { width: 58mm; padding: 3mm; text-align: center; }
        .product-name { font-size: 9px; font-weight: bold; margin-bottom: 2mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .barcode-wrapper { margin: 2mm 0; display: flex; justify-content: center; }
        .barcode-wrapper svg { max-width: 100%; height: 15mm; }
        .barcode-number { font-size: 8px; font-weight: bold; letter-spacing: 1px; margin: 1mm 0; }
        .price { font-size: 12px; font-weight: bold; margin-top: 1mm; }
        .print-button {
            position: fixed; top: 20px; right: 20px; padding: 12px 24px;
            background: #3b82f6; color: white; border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3); transition: all 0.2s;
            z-index: 999;
        }
        .print-button:hover { background: #2563eb; }
        @media print { .print-button { display: none; } }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()"><i class="fas fa-print"></i> Print</button>

    <div class="barcode-container">
        <div class="product-name">{{ Str::upper($product->name) }}</div>
        <div class="barcode-wrapper">
            <svg id="barcodeSvg"></svg>
        </div>
        <div class="barcode-number">{{ $product->barcode ?? $product->sku }}</div>
        <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        try {
            JsBarcode("#barcodeSvg", "{{ $product->barcode ?? $product->sku }}", {
                format: "CODE128",
                width: 1.2,
                height: 45,
                displayValue: false,
                font: "monospace",
                fontOptions: "bold",
                margin: 0,
            });
        } catch(e) {
            document.getElementById('barcodeSvg').innerHTML = '<text x="50%" y="50%" text-anchor="middle" fill="#999" font-size="10">Invalid</text>';
        }
    </script>
</body>
</html>
