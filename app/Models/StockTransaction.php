<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'medical_stock_item_id', 'transaction_type', 'quantity', 'unit_cost',
        'supplier', 'clinic_visit_id', 'transaction_date', 'recorded_by', 'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity'         => 'integer',
        'unit_cost'        => 'decimal:2',
    ];

    public function item(): BelongsTo         { return $this->belongsTo(MedicalStockItem::class, 'medical_stock_item_id'); }
    public function visit(): BelongsTo        { return $this->belongsTo(ClinicVisit::class); }
    public function recorder(): BelongsTo     { return $this->belongsTo(User::class, 'recorded_by'); }

    public function getLineTotalAttribute(): float
    {
        return $this->unit_cost ? (float) $this->quantity * (float) $this->unit_cost : 0;
    }
}
