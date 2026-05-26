<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpdObservation extends Model
{
    protected $fillable = [
        'teacher_user_id', 'observer_user_id', 'observation_date',
        'subject', 'class_observed', 'topic', 'strengths',
        'areas_for_improvement', 'recommendations', 'teacher_reflection', 'rating',
    ];

    protected $casts = [
        'observation_date' => 'date',
        'rating' => 'integer',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    public function observer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'observer_user_id');
    }
}
