<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $table = 'pos_purchase_orders';

    protected $fillable = [
        'po_no', 'supplier_id', 'user_id',
        'status', 'order_date', 'expected_date', 'received_date',
        'subtotal', 'discount', 'tax', 'total', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public static function generatePoNo(): string
    {
        $prefix = 'PO-' . now()->format('Ymd');
        $last = static::where('po_no', 'like', $prefix . '-%')
            ->orderBy('po_no', 'desc')
            ->value('po_no');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
