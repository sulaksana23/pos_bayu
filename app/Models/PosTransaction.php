<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTransaction extends Model
{
    protected $table = 'pos_transactions';
    protected $fillable = [
        'invoice_no','shift_id','user_id','customer_id',
        'subtotal','discount','tax','total','paid','change_amount',
        'payment_method','payment_details','status','notes',
        'void_reason','voided_at','voided_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2','discount' => 'decimal:2',
        'tax' => 'decimal:2','total' => 'decimal:2',
        'paid' => 'decimal:2','change_amount' => 'decimal:2',
        'payment_details' => 'array',
        'voided_at' => 'datetime',
    ];

    public function shift(): BelongsTo { return $this->belongsTo(PosShift::class, 'shift_id'); }
    public function cashier(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function items(): HasMany { return $this->hasMany(PosTransactionItem::class, 'transaction_id'); }
    public function stockMovements(): HasMany { return $this->hasMany(PosStockMovement::class, 'reference_id')->where('reference_type','transaction'); }

    public function scopeCompleted($query) { return $query->where('status', 'completed'); }

    public static function generateInvoiceNo(): string
    {
        return \App\Models\PosInvoiceCounter::nextForToday();
    }

    public function getFormattedTotalAttribute(): string {
        return 'Rp '.number_format((float)$this->total, 0, ',', '.');
    }
}
