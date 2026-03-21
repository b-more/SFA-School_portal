<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'priority',
        'target_type', 'target_section_id', 'target_grade_id', 'target_class_id', 'target_student_id',
        'attachment', 'posted_by', 'published_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function targetSection(): BelongsTo
    {
        return $this->belongsTo(SchoolSection::class, 'target_section_id');
    }

    public function targetGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'target_grade_id');
    }

    public function targetClass(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'target_class_id');
    }

    public function targetStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'target_student_id');
    }

    /**
     * Get the target audience description
     */
    public function getAudienceAttribute(): string
    {
        return match ($this->target_type) {
            'school' => 'Whole School',
            'section' => $this->targetSection?->name ?? 'Section',
            'grade' => $this->targetGrade?->name ?? 'Grade',
            'class' => ($this->targetGrade?->name ?? '') . ' ' . ($this->targetClass?->name ?? 'Class'),
            'student' => $this->targetStudent?->name ?? 'Student',
            default => 'Unknown',
        };
    }

    /**
     * Scope: active and published notices
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Scope: notices visible to a specific student
     */
    public function scopeForStudent(Builder $query, Student $student): Builder
    {
        return $query->where(function ($q) use ($student) {
            // Whole school
            $q->where('target_type', 'school');

            // Student's section (primary/secondary)
            if ($student->grade?->school_section_id) {
                $q->orWhere(function ($sq) use ($student) {
                    $sq->where('target_type', 'section')
                       ->where('target_section_id', $student->grade->school_section_id);
                });
            }

            // Student's grade
            if ($student->grade_id) {
                $q->orWhere(function ($sq) use ($student) {
                    $sq->where('target_type', 'grade')
                       ->where('target_grade_id', $student->grade_id);
                });
            }

            // Student's class
            if ($student->class_section_id) {
                $q->orWhere(function ($sq) use ($student) {
                    $sq->where('target_type', 'class')
                       ->where('target_class_id', $student->class_section_id);
                });
            }

            // Specific student
            $q->orWhere(function ($sq) use ($student) {
                $sq->where('target_type', 'student')
                   ->where('target_student_id', $student->id);
            });
        });
    }
}
