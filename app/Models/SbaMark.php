<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SbaMark extends Model
{
    protected $fillable = [
        'student_id', 'subject_id', 'class_section_id', 'recorded_by',
        'score', 'max_score', 'term_id', 'academic_year_id',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function percentage(): ?float
    {
        return $this->max_score > 0 ? round($this->score / $this->max_score * 100, 2) : null;
    }
}
