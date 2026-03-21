<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_section_id',
        'date_of_birth',
        'place_of_birth',
        'religious_denomination',
        'standard_of_education',
        'smallpox_vaccination',
        'date_vaccinated',
        'gender',
        'school_class_id',
        'address',
        'student_id_number',
        'parent_guardian_id',
        'grade_id',
        'academic_year_id',
        'admission_date',
        'enrollment_term_id',
        'enrollment_status',
        'user_id',
        'previous_school',
        'profile_photo',
        'medical_information',
        'notes',
        'role',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (!$student->academic_year_id) {
                $student->academic_year_id = AcademicYear::where('is_active', true)->first()?->id;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function parentGuardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class);
    }

    public function enrollmentTerm(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'enrollment_term_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function homeworkSubmissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function bookLoans(): HasMany
    {
        return $this->hasMany(BookLoan::class);
    }

    public function busPayments(): HasMany
    {
        return $this->hasMany(BusPayment::class);
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(StudentSubjectEnrollment::class);
    }

    public function optionalSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject_enrollments')
            ->withPivot('grade_id', 'academic_year_id', 'enrolled_by')
            ->withTimestamps();
    }

    /**
     * Get all active subjects for this student: mandatory grade subjects + enrolled optional ones.
     */
    public function getActiveSubjects($academicYearId): \Illuminate\Support\Collection
    {
        $gradeId = $this->grade_id;
        if (!$gradeId) {
            return collect();
        }

        // Get all grade subjects with mandatory flag
        $gradeSubjects = GradeSubject::where('grade_id', $gradeId)
            ->with('subject')
            ->get();

        $mandatorySubjectIds = $gradeSubjects->where('is_mandatory', true)->pluck('subject_id');
        $optionalSubjectIds = $gradeSubjects->where('is_mandatory', false)->pluck('subject_id');

        // Get enrolled optional subjects
        $enrolledOptionalIds = StudentSubjectEnrollment::where('student_id', $this->id)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('subject_id', $optionalSubjectIds)
            ->pluck('subject_id');

        $allSubjectIds = $mandatorySubjectIds->merge($enrolledOptionalIds)->unique();

        return Subject::whereIn('id', $allSubjectIds)->orderBy('name')->get();
    }

    /**
     * Check if student is enrolled in a subject (mandatory or optional enrollment).
     */
    public function isEnrolledInSubject($subjectId, $academicYearId): bool
    {
        $gradeId = $this->grade_id;
        if (!$gradeId) {
            return false;
        }

        // Check if mandatory
        $gradeSubject = GradeSubject::where('grade_id', $gradeId)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$gradeSubject) {
            return false;
        }

        if ($gradeSubject->is_mandatory) {
            return true;
        }

        // Check optional enrollment
        return StudentSubjectEnrollment::where('student_id', $this->id)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->exists();
    }
}
