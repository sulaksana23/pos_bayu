<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'pos_customers';
    protected $fillable = ['name','phone','email','address','points','total_spent','visit_count','notes','is_active'];

    protected $casts = ['is_active'=>'boolean','points'=>'integer','total_spent'=>'decimal:2','visit_count'=>'integer'];

    public function transactions(): HasMany { return $this->hasMany(PosTransaction::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
