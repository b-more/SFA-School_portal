<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicVisit extends Model
{
    protected $fillable = [
        'visit_date', 'student_id', 'student_name', 'grade', 'grade_level',
        'complaint_notes', 'sick_note_issued', 'outcome',
        'recorded_by', 'needs_review',
    ];

    protected $casts = [
        'visit_date'       => 'date',
        'sick_note_issued' => 'boolean',
        'needs_review'     => 'boolean',
    ];

    protected static function booted(): void
    {
        // Normalize grade string → integer 1..12 for reporting.
        // "10"→10, "Form 1"→8, "Grade 5"→5, "Form 3"→10.
        static::saving(function (self $v) {
            if ($v->grade_level === null && $v->grade) {
                $v->grade_level = self::normalizeGrade($v->grade);
            }
        });
    }

    public static function normalizeGrade(?string $raw): ?int
    {
        if (! $raw) return null;
        $raw = trim($raw);
        // "Form 1" = Grade 8, "Form 2" = 9, "Form 3" = 10, "Form 4" = 11, "Form 5" = 12
        if (preg_match('/Form\s*(\d+)/i', $raw, $m)) {
            return min(12, 7 + (int) $m[1]);
        }
        if (preg_match('/(\d+)/', $raw, $m)) {
            $n = (int) $m[1];
            return $n >= 1 && $n <= 12 ? $n : null;
        }
        return null;
    }

    public function student(): BelongsTo         { return $this->belongsTo(Student::class); }
    public function complaints(): BelongsToMany  { return $this->belongsToMany(ClinicComplaint::class, 'clinic_visit_clinic_complaint'); }
    public function stockTransactions(): HasMany { return $this->hasMany(StockTransaction::class); }
    public function recorder(): BelongsTo        { return $this->belongsTo(User::class, 'recorded_by'); }
}
