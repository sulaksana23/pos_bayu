<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PosInvoiceCounter extends Model {
    protected $table = 'pos_invoice_counters';
    protected $fillable = ['date','last_seq'];
    protected $casts = ['date'=>'date','last_seq'=>'integer'];

    /**
     * Atomically generate the next invoice number for the current day.
     * Uses upsert with ON CONFLICT to be race-free across concurrent cashiers.
     * Works on both PostgreSQL and SQLite (≥3.24).
     */
    public static function nextForToday(): string {
        $today = now()->format('Ymd');
        $now = now();
        return DB::transaction(function () use ($today, $now) {
            DB::statement(
                'INSERT INTO pos_invoice_counters (date, last_seq, created_at, updated_at)
                 VALUES (?, 1, ?, ?)
                 ON CONFLICT (date) DO UPDATE SET
                   last_seq = pos_invoice_counters.last_seq + 1,
                   updated_at = EXCLUDED.updated_at',
                [$today, $now, $now]
            );
            $seq = (int) DB::table('pos_invoice_counters')->where('date', $today)->value('last_seq');
            return 'TRX'.$today.'-'.str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
        });
    }
}
