<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosStockMovement extends Model
{
    protected $table = 'pos_stock_movements';
    protected $fillable = [
        'product_id','user_id','type','qty','unit_cost',
        'reference_type','reference_id','notes',
    ];
    protected $casts = ['qty'=>'integer','unit_cost'=>'decimal:2'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
