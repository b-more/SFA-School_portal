<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalStockItem extends Model
{
    protected $fillable = ['name', 'category', 'unit', 'reorder_level', 'is_active', 'notes'];

    protected $casts = ['is_active' => 'boolean', 'reorder_level' => 'integer'];

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Live balance = opening + purchases − usage − expired_damaged ± adjustments.
     * Adjustments are signed (negative quantity subtracts) so a correction can
     * push either direction. Computed via SQL SUM so it stays fresh without
     * denormalising.
     */
    public function getCurrentBalanceAttribute(): int
    {
        $agg = $this->transactions()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN transaction_type IN ('purchase','opening') THEN quantity ELSE 0 END), 0) AS credit,
                COALESCE(SUM(CASE WHEN transaction_type IN ('usage','expired_damaged') THEN quantity ELSE 0 END), 0) AS debit,
                COALESCE(SUM(CASE WHEN transaction_type = 'adjustment' THEN quantity ELSE 0 END), 0) AS adjust
            ")->first();

        return (int) (($agg->credit ?? 0) - ($agg->debit ?? 0) + ($agg->adjust ?? 0));
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_balance <= $this->reorder_level;
    }
}
