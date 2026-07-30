<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingScale extends Model
{
    protected $fillable = [
        'name',
        'grade_level',
        'description',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the grading scale items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(GradingScaleItem::class)->orderBy('sort_order')->orderByDesc('min_marks');
    }

    /**
     * Scope for active grading scales.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for grading scales by grade level.
     */
    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->where(function ($q) use ($gradeLevel) {
            $q->where('grade_level', $gradeLevel)
              ->orWhere('grade_level', 'all');
        });
    }

    /**
     * Get the default grading scale for a grade level.
     */
    public static function getDefaultForGradeLevel(string $gradeLevel): ?self
    {
        return static::active()
            ->forGradeLevel($gradeLevel)
            ->where('is_default', true)
            ->first()
            ?? static::active()
                ->forGradeLevel($gradeLevel)
                ->first();
    }

    /**
     * Determine grade level from a Grade model.
     *
     * Zambian mapping:
     *   • Baby Class / Middle Class / Reception → primary (ECL)
     *   • Grade 1–7 → primary
     *   • Grade 8–12 → secondary
     *   • Form 1 upward → secondary (this school still uses Form naming
     *     for lower secondary alongside Grade 10–12 for upper secondary,
     *     so any Form N is treated as secondary regardless of number).
     */
    public static function determineGradeLevelFromGrade(Grade $grade): string
    {
        $gradeName = $grade->name ?? '';

        if (in_array($gradeName, ['Baby Class', 'Middle Class', 'Reception'])) {
            return 'primary';
        }

        if (preg_match('/Form\s*\d+/i', $gradeName)) {
            return 'secondary';
        }

        if (preg_match('/Grade\s*(\d+)/i', $gradeName, $matches)) {
            $gradeNumber = (int) $matches[1];
            return $gradeNumber <= 7 ? 'primary' : 'secondary';
        }

        // Unknown naming: fall back to primary (safer default for lower
        // grades; a Form/Grade 8+ pupil would only end up here if the
        // grade was renamed to something exotic, which is worth flagging).
        return 'primary';
    }

    /**
     * Calculate grade from marks using this scale.
     */
    public function calculateGrade(float $marks): ?GradingScaleItem
    {
        return $this->items()
            ->where('min_marks', '<=', $marks)
            ->where('max_marks', '>=', $marks)
            ->first();
    }

    /**
     * Get grade letter from marks.
     */
    public function getGradeLetter(float $marks): string
    {
        $item = $this->calculateGrade($marks);
        return $item ? $item->grade : 'N/A';
    }

    /**
     * Get grade remark from marks.
     */
    public function getGradeRemark(float $marks): string
    {
        $item = $this->calculateGrade($marks);
        return $item ? ($item->remark ?? '') : '';
    }

    /**
     * Get grade points from marks.
     */
    public function getGradePoints(float $marks): float
    {
        $item = $this->calculateGrade($marks);
        return $item ? $item->grade_points : 0;
    }
}
