<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestionCriterion extends Model
{
    public $timestamps = false;

    protected $table = 'assessment_question_criteria';

    protected $fillable = [
        'assessment_question_id', 'criterion', 'max_marks', 'position',
    ];

    protected $casts = [
        'max_marks' => 'integer',
        'position' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
}
