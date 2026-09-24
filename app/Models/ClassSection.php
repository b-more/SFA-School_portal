<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade_id',
        'academic_year_id',
        'class_teacher_id',
        'capacity',
        'description',
        'is_active',
        'code',
    ];

    /**
     * Keep teachers.class_section_id + teachers.is_class_teacher in sync with
     * class_sections.class_teacher_id. When the class teacher of a section changes:
     *   - The previous teacher (if any) is unlinked from this section.
     *   - The new teacher (if any) is linked to this section.
     * Prevents the homeroom <-> attendance gate from breaking after a swap.
     */
    protected static function booted(): void
    {
        static::saved(function (ClassSection $section) {
            if (! $section->wasChanged('class_teacher_id')) {
                return;
            }

            $previousId = $section->getOriginal('class_teacher_id');
            $newId = $section->class_teacher_id;

            if ($previousId && $previousId !== $newId) {
                Teacher::where('id', $previousId)
                    ->where('class_section_id', $section->id)
                    ->update([
                        'class_section_id' => null,
                        'is_class_teacher' => false,
                    ]);
            }

            if ($newId) {
                Teacher::where('id', $newId)->update([
                    'class_section_id' => $section->id,
                    'is_class_teacher' => true,
                ]);
            }
        });
    }

    /**
     * Get the grade that this class section belongs to
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the academic year that this class section belongs to
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the teacher assigned as the class teacher
     */
    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    /**
     * Get the teachers assigned to this class section (for all roles)
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class_section', 'class_section_id', 'teacher_id')
                    ->withTimestamps();
    }

    /**
     * Get students in this class section
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get all subject teachings for this class section
     */
    public function subjectTeachings(): HasMany
    {
        return $this->hasMany(SubjectTeaching::class);
    }

    /**
     * Get all timetable entries for this class section
     */
    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

    /**
     * Get timetable for a specific academic year
     */
    public function getTimetable(?int $academicYearId = null): array
    {
        $yearId = $academicYearId ?? AcademicYear::current()?->id;
        return $yearId ? TimetableEntry::getClassTimetable($this->id, $yearId) : [];
    }

    /**
     * Get the employees associated with this class section through teachers
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'teacher_class_section', 'class_section_id', 'teacher_id')
                   ->using(TeacherClassSection::class)
                   ->withTimestamps();
    }

    /**
     * Generate a code for a class section based on grade code and section name
     */
    public static function generateCode($gradeCode, $sectionName)
    {
        return $gradeCode . '-' . strtoupper(substr($sectionName, 0, 1));
    }

    /**
     * Check if section is at capacity
     */
    public function isAtCapacity()
    {
        return $this->students()->count() >= $this->capacity;
    }
}
