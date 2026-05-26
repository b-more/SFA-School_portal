<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpdGoal extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'term', 'academic_year',
        'status', 'target_date', 'achieved_date', 'evidence',
    ];

    protected $casts = [
        'target_date' => 'date',
        'achieved_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
