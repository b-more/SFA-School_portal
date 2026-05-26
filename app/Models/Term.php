<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_current',
        'academic_year_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
    ];

    /**
     * Keep is_active and is_current in lock-step.
     *
     * The schema has two booleans (is_active, is_current) that mean the same
     * thing in practice but were introduced at different times. The admin form
     * sets is_active; the dashboard widgets read is_current. This observer
     * mirrors any change so admins only have to set one — and unsets every
     * other term, guaranteeing exactly one canonical "current term".
     */
    protected static function booted(): void
    {
        static::saving(function (self $term) {
            if ($term->isDirty('is_active') && ! $term->isDirty('is_current')) {
                $term->is_current = (bool) $term->is_active;
            } elseif ($term->isDirty('is_current') && ! $term->isDirty('is_active')) {
                $term->is_active = (bool) $term->is_current;
            }
        });

        static::saved(function (self $term) {
            if ($term->is_active || $term->is_current) {
                static::query()
                    ->where('id', '!=', $term->id)
                    ->where(function ($q) {
                        $q->where('is_active', true)->orWhere('is_current', true);
                    })
                    ->update(['is_active' => false, 'is_current' => false]);
            }
        });
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    // Make sure only one term can be active at a time
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_active) {
                static::where('id', '!=', $model->id)
                    ->where('academic_year_id', $model->academic_year_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });

        // Clear cache when term is saved or deleted
        static::saved(function ($model) {
            app(\App\Services\CacheService::class)->clearTermCache();
            // Also clear fee structure cache for this term
            if ($model->id) {
                app(\App\Services\CacheService::class)->clearFeeStructureCache($model->id);
            }
        });

        static::deleted(function ($model) {
            app(\App\Services\CacheService::class)->clearTermCache();
            if ($model->id) {
                app(\App\Services\CacheService::class)->clearFeeStructureCache($model->id);
            }
        });
    }

    // Get current term
    public static function current()
    {
        return self::where('is_active', true)->first();
    }
}
