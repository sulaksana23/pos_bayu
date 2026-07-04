<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransactionItem extends Model
{
    protected $table = 'pos_transaction_items';
    protected $fillable = [
        'transaction_id','product_id','product_name','product_sku',
        'price','cost','qty','discount','subtotal',
    ];
    protected $casts = [
        'price'=>'decimal:2','cost'=>'decimal:2','qty'=>'integer',
        'discount'=>'decimal:2','subtotal'=>'decimal:2',
    ];

    public function transaction(): BelongsTo { return $this->belongsTo(PosTransaction::class,'transaction_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
