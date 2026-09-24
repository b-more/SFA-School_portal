<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCategory extends Model
{
    protected $fillable = [
        'code', 'name', 'frequency', 'default_amount',
        'narration_template', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const TUITION          = 'tuition';
    public const BUS              = 'bus';
    public const PTA              = 'pta';
    public const COMPUTER         = 'computer';
    public const MAINTENANCE      = 'maintenance';
    public const UNIFORM          = 'uniform';
    public const EDUCATIONAL_TOUR = 'educational_tour';

    public const ANNUAL_AUTO_CODES = [self::PTA, self::COMPUTER, self::MAINTENANCE];

    public function catalogueItems(): HasMany
    {
        return $this->hasMany(FeeCatalogueItem::class);
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    /** Render the SMS/receipt label for a given period (e.g. "Bus (May 2026)"). */
    public function narration(?string $period = null): string
    {
        return strtr($this->narration_template ?? '{category} ({period})', [
            '{category}' => $this->name,
            '{period}'   => $period ?? '',
        ]);
    }
}
