<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosShift extends Model
{
    protected $table = 'pos_shifts';
    protected $fillable = [
        'user_id','opening_cash','closing_cash','expected_cash','cash_difference',
        'transaction_count','total_sales','total_cash','total_non_cash',
        'opened_at','closed_at','status','opening_notes','closing_notes',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_non_cash' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function transactions(): HasMany { return $this->hasMany(PosTransaction::class, 'shift_id'); }

    public function isOpen(): bool { return $this->status === 'open'; }

    public function getDurationAttribute(): ?string {
        if (!$this->closed_at) return null;
        $diff = $this->closed_at->diffInMinutes($this->opened_at);
        $h = intdiv($diff, 60); $m = $diff % 60;
        return "{$h}h {$m}m";
    }

    public function recalculateTotals(): void {
        $this->transaction_count = $this->transactions()->where('status','completed')->count();
        $this->total_sales = (float) $this->transactions()->where('status','completed')->sum('total');
        $this->total_cash = (float) $this->transactions()->where('status','completed')->where('payment_method','cash')->sum('paid');
        $this->total_non_cash = (float) $this->transactions()->where('status','completed')->whereIn('payment_method',['qris','transfer','wallet'])->sum('paid');
        $this->expected_cash = (float)$this->opening_cash + $this->total_cash - (float) $this->transactions()->where('status','completed')->where('payment_method','cash')->sum('change_amount');
        $this->save();
    }
}
