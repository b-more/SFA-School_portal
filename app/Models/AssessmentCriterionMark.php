<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentCriterionMark extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'assessment_submission_id', 'assessment_question_criterion_id', 'marks_awarded',
    ];

    protected $casts = [
        'marks_awarded' => 'decimal:2',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssessmentSubmission::class, 'assessment_submission_id');
    }
}
