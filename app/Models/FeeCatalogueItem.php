<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCatalogueItem extends Model
{
    protected $fillable = ['fee_category_id', 'name', 'amount', 'description', 'is_active'];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class, 'catalogue_item_id');
    }
}
