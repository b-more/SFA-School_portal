<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    protected $fillable = [
        'assessment_id', 'question_text', 'points', 'position', 'source_bank_item_id',
    ];

    protected $casts = [
        'points' => 'integer',
        'position' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(AssessmentQuestionCriterion::class)->orderBy('position');
    }
}
