<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBankItem extends Model
{
    protected $fillable = [
        'created_by',
        'subject_id',
        'grade_id',
        'topic',
        'curriculum',
        'component',
        'type',
        'question_text',
        'max_marks',
        'difficulty',
        'model_answer',
        'is_shared',
        'academic_year_id',
        'term_id',
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'max_marks' => 'integer',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(QuestionBankOption::class)->orderBy('position');
    }

    public function rubricCriteria(): HasMany
    {
        return $this->hasMany(QuestionBankRubricCriterion::class)->orderBy('position');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function isObjective(): bool
    {
        return in_array($this->type, ['mcq', 'true_false']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->academic_year_id)) {
                $item->academic_year_id = AcademicYear::where('is_active', true)->first()?->id;
            }
            if (empty($item->term_id)) {
                $item->term_id = Term::where('is_active', true)->first()?->id;
            }
        });
    }
}
