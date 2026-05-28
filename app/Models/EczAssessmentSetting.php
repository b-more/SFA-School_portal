<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EczAssessmentSetting extends Model
{
    protected $fillable = ['theory_weight', 'sba_weight', 'updated_by'];

    protected $casts = [
        'theory_weight' => 'integer',
        'sba_weight' => 'integer',
    ];

    /** The single settings row, created with ECZ defaults (70/30) if missing. */
    public static function current(): self
    {
        return static::firstOrCreate([], ['theory_weight' => 70, 'sba_weight' => 30]);
    }
}
