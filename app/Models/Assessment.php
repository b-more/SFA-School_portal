<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'created_by', 'class_section_id', 'subject_id', 'grade_id', 'title', 'description',
        'component', 'time_limit_minutes', 'due_at', 'status', 'total_marks',
        'academic_year_id', 'term_id',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'time_limit_minutes' => 'integer',
        'total_marks' => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('position');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssessmentSubmission::class);
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
        static::creating(function ($a) {
            if (empty($a->academic_year_id)) {
                $a->academic_year_id = AcademicYear::where('is_active', true)->first()?->id;
            }
            if (empty($a->term_id)) {
                $a->term_id = Term::where('is_active', true)->first()?->id;
            }
        });
    }
}
