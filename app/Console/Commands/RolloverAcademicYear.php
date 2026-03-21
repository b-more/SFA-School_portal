<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicYear;
use App\Models\GradeSubject;
use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassSection;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\StudentSubjectEnrollment;
use App\Models\SubjectTeaching;
use Illuminate\Support\Facades\DB;

class RolloverAcademicYear extends Command
{
    protected $signature = 'academic-year:rollover 
                            {--to-year= : Target academic year ID}
                            {--dry-run : Preview changes without applying}
                            {--students : Rollover students only}
                            {--teachers : Rollover teachers only}
                            {--fees : Rollover fees only}
                            {--all : Rollover everything}';

    protected $description = 'Rollover data from one academic year to the next';

    protected $newYear;
    protected $currentYear;
    protected $stats = [
        'students_promoted' => 0,
        'students_repeated' => 0,
        'students_graduated' => 0,
        'teachers_assigned' => 0,
        'fees_generated' => 0,
        'enrollments_carried' => 0,
    ];

    public function handle()
    {
        $this->currentYear = AcademicYear::where('is_active', true)->first();
        
        if (!$this->currentYear) {
            $this->error('No active academic year found!');
            return 1;
        }

        $this->newYear = $this->option('to-year') 
            ? AcademicYear::find($this->option('to-year'))
            : AcademicYear::where('start_date', '>', $this->currentYear->end_date)
                ->orderBy('start_date')
                ->first();

        if (!$this->newYear) {
            $this->error('Target academic year not found!');
            return 1;
        }

        $this->info("Rolling over from {$this->currentYear->name} to {$this->newYear->name}");
        $this->newLine();

        if (!$this->confirm('This will create new enrollment records for the next year. Continue?', true)) {
            return 0;
        }

        DB::beginTransaction();

        try {
            if ($this->option('all') || $this->option('students')) {
                $this->rolloverStudents();
            }

            if ($this->option('all') || $this->option('teachers')) {
                $this->rolloverTeachers();
            }

            if ($this->option('all') || $this->option('fees')) {
                $this->rolloverFees();
            }

            if (!$this->option('all') && !$this->option('students') && !$this->option('teachers') && !$this->option('fees')) {
                $this->rolloverStudents();
                $this->rolloverTeachers();
                $this->rolloverFees();
            }

            // Always rollover subject enrollments after students are rolled over
            if ($this->option('all') || $this->option('students') || (!$this->option('teachers') && !$this->option('fees'))) {
                $this->rolloverSubjectEnrollments();
            }

            if ($this->option('dry-run')) {
                DB::rollBack();
                $this->warn('DRY RUN - No changes were saved');
            } else {
                DB::commit();
                $this->info('Rollover completed successfully!');
            }

            $this->displayStats();
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Rollover failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    protected function rolloverStudents(): void
    {
        $this->info('Rolling over students...');
        
        $students = Student::allYears()
            ->where('academic_year_id', $this->currentYear->id)
            ->where('enrollment_status', 'active')
            ->with(['grade', 'classSection', 'user', 'parentGuardian'])
            ->get();

        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $student) {
            $bar->advance();

            $shouldPromote = $this->shouldPromoteStudent($student);
            $nextGrade = $this->getNextGrade($student->grade_id, $shouldPromote);

            if (!$nextGrade) {
                $student->update(['enrollment_status' => 'graduated']);
                $this->stats['students_graduated']++;
                continue;
            }

            $classSection = ClassSection::allYears()
                ->where('academic_year_id', $this->newYear->id)
                ->where('grade_id', $nextGrade->id)
                ->first();

            if (!$classSection) {
                $this->warn("\nNo class section found for grade {$nextGrade->name} in {$this->newYear->name}");
                continue;
            }

            Student::withoutGlobalScope('academicYear')->create([
                'user_id' => $student->user_id,
                'name' => $student->name,
                'student_id_number' => $student->student_id_number,
                'academic_year_id' => $this->newYear->id,
                'grade_id' => $nextGrade->id,
                'class_section_id' => $classSection->id,
                'parent_guardian_id' => $student->parent_guardian_id,
                'date_of_birth' => $student->date_of_birth,
                'gender' => $student->gender,
                'address' => $student->address,
                'religious_denomination' => $student->religious_denomination,
                'medical_information' => $student->medical_information,
                'profile_photo' => $student->profile_photo,
                'enrollment_status' => 'active',
                'admission_date' => $this->newYear->start_date,
                'place_of_birth' => $student->place_of_birth,
                'smallpox_vaccination' => $student->smallpox_vaccination,
                'previous_school' => $student->previous_school,
                'role' => $student->role,
            ]);

            $student->update(['enrollment_status' => 'completed']);

            if ($shouldPromote) {
                $this->stats['students_promoted']++;
            } else {
                $this->stats['students_repeated']++;
            }
        }

        $bar->finish();
        $this->newLine();
    }

    protected function rolloverTeachers(): void
    {
        $this->info('Rolling over teacher assignments...');

        $teachings = SubjectTeaching::allYears()
            ->where('academic_year_id', $this->currentYear->id)
            ->with(['teacher', 'subject', 'classSection'])
            ->get();

        $bar = $this->output->createProgressBar($teachings->count());
        $bar->start();

        foreach ($teachings as $teaching) {
            $bar->advance();

            $newClassSection = ClassSection::allYears()
                ->where('academic_year_id', $this->newYear->id)
                ->where('grade_id', $teaching->classSection->grade_id)
                ->where('name', $teaching->classSection->name)
                ->first();

            if ($newClassSection) {
                SubjectTeaching::withoutGlobalScope('academicYear')->create([
                    'teacher_id' => $teaching->teacher_id,
                    'subject_id' => $teaching->subject_id,
                    'class_section_id' => $newClassSection->id,
                    'academic_year_id' => $this->newYear->id,
                ]);

                $this->stats['teachers_assigned']++;
            }
        }

        $bar->finish();
        $this->newLine();
    }

    protected function rolloverFees(): void
    {
        $this->info('Rolling over fee structures and generating student fees...');

        $feeStructures = FeeStructure::allYears()
            ->where('academic_year_id', $this->currentYear->id)
            ->get();

        foreach ($feeStructures as $structure) {
            FeeStructure::withoutGlobalScope('academicYear')->create([
                'academic_year_id' => $this->newYear->id,
                'name' => $structure->name,
                'description' => $structure->description,
                'amount' => $structure->amount,
                'fee_type' => $structure->fee_type,
                'grade_id' => $structure->grade_id,
                'is_mandatory' => $structure->is_mandatory,
                'due_date' => $structure->due_date,
            ]);
        }

        $students = Student::allYears()
            ->where('academic_year_id', $this->newYear->id)
            ->where('enrollment_status', 'active')
            ->get();

        foreach ($students as $student) {
            $applicableFees = FeeStructure::allYears()
                ->where('academic_year_id', $this->newYear->id)
                ->where(function ($q) use ($student) {
                    $q->whereNull('grade_id')
                      ->orWhere('grade_id', $student->grade_id);
                })
                ->get();

            foreach ($applicableFees as $fee) {
                StudentFee::withoutGlobalScope('academicYear')->create([
                    'student_id' => $student->id,
                    'fee_structure_id' => $fee->id,
                    'academic_year_id' => $this->newYear->id,
                    'amount_due' => $fee->amount,
                    'amount_paid' => 0,
                    'balance' => $fee->amount,
                    'status' => 'pending',
                ]);

                $this->stats['fees_generated']++;
            }
        }
    }

    protected function rolloverSubjectEnrollments(): void
    {
        $this->info('Rolling over optional subject enrollments...');

        // Get all new-year students and match them to their old-year counterparts via user_id
        $newStudents = Student::allYears()
            ->where('academic_year_id', $this->newYear->id)
            ->where('enrollment_status', 'active')
            ->get();

        $bar = $this->output->createProgressBar($newStudents->count());
        $bar->start();

        foreach ($newStudents as $newStudent) {
            $bar->advance();

            // Find the old student record by user_id in the current year
            $oldStudent = Student::allYears()
                ->where('academic_year_id', $this->currentYear->id)
                ->where('user_id', $newStudent->user_id)
                ->first();

            if (!$oldStudent) {
                continue;
            }

            // Get old enrollments
            $oldEnrollments = StudentSubjectEnrollment::where('student_id', $oldStudent->id)
                ->where('academic_year_id', $this->currentYear->id)
                ->get();

            foreach ($oldEnrollments as $enrollment) {
                // Check if the subject is still offered as optional in the new grade
                $stillOptional = GradeSubject::where('grade_id', $newStudent->grade_id)
                    ->where('subject_id', $enrollment->subject_id)
                    ->where('is_mandatory', false)
                    ->exists();

                if ($stillOptional) {
                    StudentSubjectEnrollment::firstOrCreate(
                        [
                            'student_id' => $newStudent->id,
                            'subject_id' => $enrollment->subject_id,
                            'academic_year_id' => $this->newYear->id,
                        ],
                        [
                            'grade_id' => $newStudent->grade_id,
                            'enrolled_by' => $enrollment->enrolled_by,
                        ]
                    );

                    $this->stats['enrollments_carried']++;
                }
            }
        }

        $bar->finish();
        $this->newLine();
    }

    protected function shouldPromoteStudent(Student $student): bool
    {
        $avgMarks = $student->results()
            ->allYears()
            ->where('academic_year_id', $this->currentYear->id)
            ->avg('marks');

        return $avgMarks >= 50;
    }

    protected function getNextGrade(int $currentGradeId, bool $promote): ?Grade
    {
        $currentGrade = Grade::find($currentGradeId);

        if (!$promote) {
            return $currentGrade;
        }

        $gradeProgression = [
            'Baby Class' => 'Middle Class',
            'Middle Class' => 'Reception',
            'Reception' => 'Grade 1',
            'Grade 1' => 'Grade 2',
            'Grade 2' => 'Grade 3',
            'Grade 3' => 'Grade 4',
            'Grade 4' => 'Grade 5',
            'Grade 5' => 'Grade 6',
            'Grade 6' => 'Grade 7',
            'Grade 7' => 'Grade 8',
            'Grade 8' => 'Grade 9',
            'Grade 9' => 'Grade 10',
            'Grade 10' => 'Grade 11',
            'Grade 11' => 'Grade 12',
            'Grade 12' => null,
        ];

        $nextGradeName = $gradeProgression[$currentGrade->name] ?? null;
        return $nextGradeName ? Grade::where('name', $nextGradeName)->first() : null;
    }

    protected function displayStats(): void
    {
        $this->newLine();
        $this->info('=== Rollover Statistics ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Students Promoted', $this->stats['students_promoted']],
                ['Students Repeated', $this->stats['students_repeated']],
                ['Students Graduated', $this->stats['students_graduated']],
                ['Teacher Assignments', $this->stats['teachers_assigned']],
                ['Fees Generated', $this->stats['fees_generated']],
                ['Subject Enrollments Carried', $this->stats['enrollments_carried']],
            ]
        );
    }
}
