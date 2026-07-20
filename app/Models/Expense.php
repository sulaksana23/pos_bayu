<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'pos_expenses';

    protected $fillable = [
        'expense_no', 'user_id', 'category',
        'amount', 'description', 'expense_date',
        'payment_method', 'receipt_image', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateExpenseNo(): string
    {
        $prefix = 'EXP-' . now()->format('Ymd');
        $last = static::where('expense_no', 'like', $prefix . '-%')
            ->orderBy('expense_no', 'desc')
            ->value('expense_no');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
