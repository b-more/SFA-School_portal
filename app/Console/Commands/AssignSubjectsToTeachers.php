<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\Teacher;
use Illuminate\Console\Command;

class AssignSubjectsToTeachers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teachers:assign-subjects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign subjects to primary teachers based on their grade assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subject assignment for teachers...');

        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        if (!$currentAcademicYear) {
            $this->error('No active academic year found!');
            return Command::FAILURE;
        }

        $this->info("Using academic year: {$currentAcademicYear->name}");

        // Get all primary teachers with grade and class section assignments
        $teachers = Teacher::whereNull('specialization') // Primary teachers have no specialization
            ->whereNotNull('grade_id')
            ->whereNotNull('class_section_id')
            ->with(['grade.subjects'])
            ->get();

        $this->info("Found {$teachers->count()} primary teachers with grade assignments");

        $successCount = 0;
        $skippedCount = 0;

        foreach ($teachers as $teacher) {
            if (!$teacher->grade) {
                $this->warn("Teacher {$teacher->name} ({$teacher->employee_id}) has no grade assigned - skipping");
                $skippedCount++;
                continue;
            }

            $subjects = $teacher->grade->subjects()->where('is_active', true)->get();

            if ($subjects->isEmpty()) {
                $this->warn("Grade {$teacher->grade->name} has no subjects - skipping teacher {$teacher->name}");
                $skippedCount++;
                continue;
            }

            // Clear existing subject teachings
            $existingCount = $teacher->subjectTeachings()->count();
            $teacher->subjectTeachings()->delete();

            // Assign all subjects for this grade
            foreach ($subjects as $subject) {
                $teacher->subjectTeachings()->create([
                    'subject_id' => $subject->id,
                    'class_section_id' => $teacher->class_section_id,
                    'academic_year_id' => $currentAcademicYear->id,
                ]);
            }

            $this->info("✓ Assigned {$subjects->count()} subjects to {$teacher->name} ({$teacher->employee_id}) for {$teacher->grade->name} - {$teacher->classSection->name}" . ($existingCount > 0 ? " (replaced {$existingCount} existing)" : ""));
            $successCount++;
        }

        $this->newLine();
        $this->info("Subject assignment complete!");
        $this->info("✓ Successfully assigned subjects to {$successCount} teachers");

        if ($skippedCount > 0) {
            $this->warn("⚠ Skipped {$skippedCount} teachers (missing grade or subjects)");
        }

        return Command::SUCCESS;
    }
}
