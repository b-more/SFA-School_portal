<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'school_section_id',
        'term_id',
        'academic_year_id',
        'basic_fee',
        'additional_charges',
        'total_fee',
        'description',
        'is_active',
        'name',
    ];

    protected $casts = [
        'additional_charges' => 'array', // Using 'array' instead of 'json' for better handling
        'basic_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Accessor to ensure additional_charges is always an array
     */
    public function getAdditionalChargesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Mutator to ensure additional_charges is always stored properly
     */
    public function setAdditionalChargesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['additional_charges'] = json_encode($value);
        } else if (is_string($value)) {
            // If it's already a JSON string, validate it first
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['additional_charges'] = $value;
            } else {
                $this->attributes['additional_charges'] = json_encode([]);
            }
        } else {
            $this->attributes['additional_charges'] = json_encode([]);
        }
    }

    /**
     * Check if a charge is a uniform/sports item (shown on PDF page 2, not charged to students)
     */
    public static function isUniformItem(string $description): bool
    {
        return str_starts_with($description, 'Girls -') ||
               str_starts_with($description, 'Boys -') ||
               str_starts_with($description, 'Sports -') ||
               $description === 'Blazer';
    }

    /**
     * Calculate the total fee based on basic fee and additional charges
     * Excludes uniform/sports items which are optional one-time purchases
     */
    public function calculateTotalFee()
    {
        $total = is_numeric($this->basic_fee) ? (float) $this->basic_fee : 0;

        $additionalCharges = $this->additional_charges;
        if (is_array($additionalCharges)) {
            foreach ($additionalCharges as $charge) {
                if (isset($charge['amount']) && is_numeric($charge['amount']) && isset($charge['description'])) {
                    if (!self::isUniformItem($charge['description'])) {
                        $total += (float) $charge['amount'];
                    }
                }
            }
        }

        return round($total, 2);
    }

    /**
     * Auto-calculate total fee before saving
     */
    protected static function booted()
    {
        static::saving(function ($feeStructure) {
            if (!isset($feeStructure->total_fee) || $feeStructure->isDirty(['basic_fee', 'additional_charges'])) {
                $feeStructure->total_fee = $feeStructure->calculateTotalFee();
            }
        });

        // Clear cache when fee structure is saved or deleted
        static::saved(function ($feeStructure) {
            if ($feeStructure->term_id) {
                app(\App\Services\CacheService::class)->clearFeeStructureCache($feeStructure->term_id);
            }
        });

        static::deleted(function ($feeStructure) {
            if ($feeStructure->term_id) {
                app(\App\Services\CacheService::class)->clearFeeStructureCache($feeStructure->term_id);
            }
        });
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function schoolSection(): BelongsTo
    {
        return $this->belongsTo(SchoolSection::class);
    }

    /**
     * Get the section name - uses direct section for new records, derives from grade for old ones
     */
    public function getSectionNameAttribute(): string
    {
        if ($this->school_section_id) {
            return $this->schoolSection?->name ?? 'Unknown Section';
        }

        return $this->grade?->schoolSection?->name ?? 'Unknown Section';
    }

    /**
     * Get grade name (for backward compatibility)
     * Shows section name for section-based records
     */
    public function getGradeNameAttribute(): string
    {
        if ($this->grade_id) {
            return $this->grade?->name ?? 'Unknown Grade';
        }

        return $this->section_name;
    }
}
