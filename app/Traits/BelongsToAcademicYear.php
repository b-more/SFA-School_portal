<?php

namespace App\Traits;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait BelongsToAcademicYear
 *
 * Automatically scopes queries to the currently selected academic year
 * and provides helper methods for working with academic year data.
 *
 * @package App\Traits
 */
trait BelongsToAcademicYear
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToAcademicYear(): void
    {
        // Auto-assign active academic year on creation
        static::creating(function ($model) {
            if (empty($model->academic_year_id)) {
                $activeYear = AcademicYear::where('is_active', true)->first();
                if ($activeYear) {
                    $model->academic_year_id = $activeYear->id;
                }
            }
        });

        // Global scope to filter by session academic year (unless disabled)
        static::addGlobalScope('academicYear', function (Builder $builder) {
            // Don't apply scope in console commands
            if (app()->runningInConsole()) {
                return;
            }

            // Check if all_years mode is explicitly enabled
            if (request()->has('all_years') || request()->get('all_years') === true) {
                return;
            }

            // Get academic year from session or use active year
            $yearId = session('selected_academic_year_id');

            if (!$yearId) {
                $activeYear = AcademicYear::where('is_active', true)->first();
                $yearId = $activeYear?->id;

                if ($yearId) {
                    session(['selected_academic_year_id' => $yearId]);
                }
            }

            if ($yearId) {
                $builder->where($builder->getModel()->getTable() . '.academic_year_id', $yearId);
            }
        });
    }

    /**
     * Relationship to academic year
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope to specific academic year
     *
     * @param Builder $query
     * @param int $yearId
     * @return Builder
     */
    public function scopeForYear(Builder $query, int $yearId): Builder
    {
        return $query->withoutGlobalScope('academicYear')
            ->where('academic_year_id', $yearId);
    }

    /**
     * Scope to get records from all years
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAllYears(Builder $query): Builder
    {
        return $query->withoutGlobalScope('academicYear');
    }

    /**
     * Scope to active year only
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActiveYear(Builder $query): Builder
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        return $query->withoutGlobalScope('academicYear')
            ->where('academic_year_id', $activeYear?->id);
    }

    /**
     * Scope to multiple years
     *
     * @param Builder $query
     * @param array $yearIds
     * @return Builder
     */
    public function scopeInYears(Builder $query, array $yearIds): Builder
    {
        return $query->withoutGlobalScope('academicYear')
            ->whereIn('academic_year_id', $yearIds);
    }

    /**
     * Scope to current and previous year
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCurrentAndPreviousYear(Builder $query): Builder
    {
        $currentYear = AcademicYear::where('is_active', true)->first();
        $previousYear = AcademicYear::where('id', '<', $currentYear?->id)
            ->orderBy('id', 'desc')
            ->first();

        $yearIds = array_filter([$currentYear?->id, $previousYear?->id]);

        return $query->withoutGlobalScope('academicYear')
            ->whereIn('academic_year_id', $yearIds);
    }

    /**
     * Check if record belongs to active academic year
     *
     * @return bool
     */
    public function isCurrentYear(): bool
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        return $this->academic_year_id === $activeYear?->id;
    }

    /**
     * Check if record is from a past academic year
     *
     * @return bool
     */
    public function isPastYear(): bool
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        return $this->academic_year_id < $activeYear?->id;
    }
}
