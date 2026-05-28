<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'class_section_id',
        'subject_id',
        'grade_id',
        'time_limit_minutes',
        'total_points',
        'shuffle_questions',
        'status',
        'due_at',
        'academic_year_id',
        'term_id',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'due_at' => 'datetime',
        'time_limit_minutes' => 'integer',
        'total_points' => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'assigned_by');
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quiz) {
            if (empty($quiz->academic_year_id)) {
                $quiz->academic_year_id = AcademicYear::where('is_active', true)->first()?->id;
            }
            if (empty($quiz->term_id)) {
                $quiz->term_id = Term::where('is_active', true)->first()?->id;
            }
        });
    }
}
