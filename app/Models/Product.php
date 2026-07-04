<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'pos_products';
    protected $fillable = [
        'category_id','name','sku','barcode','price','cost',
        'stock','min_stock','unit','image_url','description','is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost'  => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class, 'category_id'); }
    public function transactionItems(): HasMany { return $this->hasMany(PosTransactionItem::class); }
    public function stockMovements(): HasMany { return $this->hasMany(PosStockMovement::class); }

    public function scopeActive(Builder $q): Builder { return $q->where('is_active', true); }
    public function scopeLowStock(Builder $q): Builder { return $q->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0); }
    public function scopeOutOfStock(Builder $q): Builder { return $q->where('stock', '<=', 0); }

    public function getIsLowStockAttribute(): bool {
        return $this->min_stock > 0 && $this->stock <= $this->min_stock;
    }

    public function getIsOutOfStockAttribute(): bool {
        return $this->stock <= 0;
    }

    public function getFormattedPriceAttribute(): string {
        return 'Rp '.number_format((float)$this->price, 0, ',', '.');
    }
}
