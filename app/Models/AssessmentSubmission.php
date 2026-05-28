<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentSubmission extends Model
{
    protected $fillable = [
        'assessment_id', 'student_id', 'submitted_at', 'status',
        'total_score', 'total_marks', 'percentage', 'marked_by', 'marked_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'marked_at' => 'datetime',
        'total_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'total_marks' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function criterionMarks(): HasMany
    {
        return $this->hasMany(AssessmentCriterionMark::class);
    }
}
