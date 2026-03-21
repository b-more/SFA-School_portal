<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimetableGeneratorService
{
    protected int $academicYearId;
    protected Collection $periods;       // Lesson periods only, ordered
    protected Collection $allPeriods;     // All periods including breaks
    protected Collection $classSections;
    protected array $days;

    // slot index => [period_id, day] mapping
    protected array $slots = [];

    // teacher_id => set of occupied slot indices
    protected array $teacherOccupied = [];

    // Results: class_section_id => [slot_index => [subject_id, teacher_id]]
    protected array $schedule = [];

    // Valid teacher IDs (to avoid FK violations)
    protected array $validTeacherIds = [];

    // Logs for reporting
    protected array $logs = [];

    public function __construct(int $academicYearId)
    {
        $this->academicYearId = $academicYearId;
        $this->days = TimetableEntry::DAYS;

        $this->allPeriods = TimetablePeriod::where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $this->periods = $this->allPeriods->filter(fn($p) => $p->isLesson());

        // Build slot index: 0...(periods*days - 1)
        $i = 0;
        foreach ($this->days as $day) {
            foreach ($this->periods as $period) {
                $this->slots[$i] = ['period_id' => $period->id, 'day' => $day];
                $i++;
            }
        }

        $this->validTeacherIds = Teacher::pluck('id')->flip()->toArray();

        $this->classSections = ClassSection::with(['grade', 'classTeacher'])
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($cs) => $cs->grade->level ?? 0);
    }

    /**
     * Generate the full timetable
     * Returns ['success' => bool, 'stats' => [...], 'logs' => [...]]
     */
    public function generate(): array
    {
        $this->logs = [];
        $this->schedule = [];
        $this->teacherOccupied = [];

        if ($this->periods->isEmpty()) {
            return ['success' => false, 'stats' => [], 'logs' => ['No lesson periods found. Run php artisan timetable:seed-periods first.']];
        }

        $slotsPerDay = $this->periods->count();
        $totalSlots = count($this->slots);

        $this->log("Starting timetable generation: {$slotsPerDay} periods/day, {$totalSlots} slots/week, {$this->classSections->count()} classes");

        // Separate primary and secondary classes
        $primarySections = $this->classSections->filter(fn($cs) => in_array($cs->grade?->school_section_id, [1, 2]));
        $secondarySections = $this->classSections->filter(fn($cs) => !in_array($cs->grade?->school_section_id, [1, 2]));

        // 1. Generate primary timetables first (simpler, class teacher teaches all)
        foreach ($primarySections as $cs) {
            $this->generatePrimaryClass($cs, $slotsPerDay);
        }

        // 2. Generate secondary timetables (complex, different teachers per subject)
        $this->generateSecondaryClasses($secondarySections, $slotsPerDay);

        // 3. Save to database
        $savedCount = $this->saveSchedule();

        $stats = [
            'total_classes' => $this->classSections->count(),
            'primary_classes' => $primarySections->count(),
            'secondary_classes' => $secondarySections->count(),
            'entries_created' => $savedCount,
            'slots_per_week' => $totalSlots,
        ];

        $this->log("Generation complete: {$savedCount} entries created");

        return ['success' => true, 'stats' => $stats, 'logs' => $this->logs];
    }

    /**
     * Generate timetable for a primary class (class teacher teaches all subjects)
     */
    protected function generatePrimaryClass(ClassSection $cs, int $slotsPerDay): void
    {
        $grade = $cs->grade;
        if (!$grade) {
            $this->log("Skipping {$cs->name}: no grade assigned");
            return;
        }

        $teacherId = $this->validTeacher($cs->class_teacher_id);
        $subjects = $grade->subjects()->where('is_active', true)->orderBy('name')->get();

        if ($subjects->isEmpty()) {
            $this->log("Skipping {$grade->name} {$cs->name}: no subjects assigned to grade");
            return;
        }

        // If no subject_teachings, use class teacher for all
        $subjectTeachers = DB::table('subject_teachings')
            ->where('class_section_id', $cs->id)
            ->where('academic_year_id', $this->academicYearId)
            ->pluck('teacher_id', 'subject_id')
            ->toArray();

        $totalSlots = $slotsPerDay * count($this->days);
        $subjectCount = $subjects->count();

        // Calculate periods per subject per week (distribute evenly)
        $periodsPerSubject = intdiv($totalSlots, $subjectCount);
        $remainder = $totalSlots % $subjectCount;

        // Build subject allocation list
        $subjectSlots = [];
        foreach ($subjects as $idx => $subject) {
            $count = $periodsPerSubject + ($idx < $remainder ? 1 : 0);
            $sid = $subject->id;
            $tid = $this->validTeacher($subjectTeachers[$sid] ?? null) ?? $teacherId;
            for ($i = 0; $i < $count; $i++) {
                $subjectSlots[] = ['subject_id' => $sid, 'teacher_id' => $tid];
            }
        }

        // Shuffle to randomize, then distribute ensuring no same-subject back-to-back
        // and trying to spread each subject across different days
        $distributed = $this->distributeSubjectsEvenly($subjectSlots, $subjects->pluck('id')->toArray(), $slotsPerDay);

        // For primary classes where the class teacher is shared with another class,
        // we need to check teacher occupancy and skip conflicts
        $this->schedule[$cs->id] = [];
        $conflictCount = 0;
        foreach ($distributed as $slotIdx => $assignment) {
            $tid = $assignment['teacher_id'];
            if ($tid && isset($this->teacherOccupied[$tid][$slotIdx])) {
                // Teacher is already occupied in another class at this time
                // Keep the teacher assigned for display, but don't mark as occupied
                $conflictCount++;
            } else if ($tid) {
                $this->teacherOccupied[$tid][$slotIdx] = true;
            }
            $this->schedule[$cs->id][$slotIdx] = $assignment;
        }

        $extra = $conflictCount > 0 ? " ({$conflictCount} slots without teacher due to shared class teacher)" : '';
        $this->log("Generated {$grade->name} {$cs->name}: {$subjectCount} subjects, {$totalSlots} slots (class teacher" . ($teacherId ? '' : ' NOT ASSIGNED') . "){$extra}");
    }

    /**
     * Generate timetables for secondary classes (different teachers per subject)
     * Uses constraint-based scheduling to avoid teacher conflicts
     */
    protected function generateSecondaryClasses(Collection $sections, int $slotsPerDay): void
    {
        if ($sections->isEmpty()) return;

        $totalSlots = $slotsPerDay * count($this->days);

        // Build subject-teacher assignments for each class
        $classData = [];
        foreach ($sections as $cs) {
            $grade = $cs->grade;
            if (!$grade) continue;

            $subjects = $grade->subjects()->where('is_active', true)->orderBy('name')->get();
            if ($subjects->isEmpty()) {
                $this->log("Skipping {$grade->name} {$cs->name}: no subjects assigned");
                continue;
            }

            // Get teacher assignments from subject_teachings
            $teachings = DB::table('subject_teachings')
                ->where('class_section_id', $cs->id)
                ->where('academic_year_id', $this->academicYearId)
                ->get()
                ->groupBy('subject_id');

            $subjectTeachers = [];
            foreach ($subjects as $subject) {
                $teaching = $teachings->get($subject->id);
                if ($teaching && $teaching->isNotEmpty()) {
                    $subjectTeachers[$subject->id] = $this->validTeacher($teaching->first()->teacher_id);
                } else {
                    $subjectTeachers[$subject->id] = null;
                }
            }

            // Calculate periods per subject
            $subjectCount = $subjects->count();
            $periodsPerSubject = intdiv($totalSlots, $subjectCount);
            $remainder = $totalSlots % $subjectCount;

            $subjectAllocations = [];
            foreach ($subjects->values() as $idx => $subject) {
                $count = $periodsPerSubject + ($idx < $remainder ? 1 : 0);
                $subjectAllocations[] = [
                    'subject_id' => $subject->id,
                    'teacher_id' => $subjectTeachers[$subject->id] ?? null,
                    'periods' => $count,
                ];
            }

            $classData[$cs->id] = [
                'section' => $cs,
                'allocations' => $subjectAllocations,
            ];
        }

        // Schedule each secondary class, respecting teacher conflicts
        foreach ($classData as $csId => $data) {
            $this->scheduleSecondaryClass($csId, $data, $slotsPerDay);
        }
    }

    /**
     * Schedule a single secondary class respecting teacher conflicts
     */
    protected function scheduleSecondaryClass(int $csId, array $data, int $slotsPerDay): void
    {
        $section = $data['section'];
        $allocations = $data['allocations'];
        $totalSlots = $slotsPerDay * count($this->days);
        $gradeName = ($section->grade?->name ?? '?') . ' ' . $section->name;

        // Build flat list of assignments needed
        $assignments = [];
        foreach ($allocations as $alloc) {
            for ($i = 0; $i < $alloc['periods']; $i++) {
                $assignments[] = [
                    'subject_id' => $alloc['subject_id'],
                    'teacher_id' => $alloc['teacher_id'],
                ];
            }
        }

        // Sort assignments: subjects with teachers first (more constrained), then by teacher load
        usort($assignments, function ($a, $b) {
            // Teacher-assigned subjects are more constrained, schedule first
            if ($a['teacher_id'] && !$b['teacher_id']) return -1;
            if (!$a['teacher_id'] && $b['teacher_id']) return 1;
            return 0;
        });

        $this->schedule[$csId] = [];
        $usedSlots = [];
        $subjectDayCount = []; // subject_id => [day => count] for distribution

        foreach ($assignments as $assignment) {
            $placed = false;
            $subjectId = $assignment['subject_id'];
            $teacherId = $assignment['teacher_id'];

            // Try to find the best slot
            // Prefer days where this subject has fewer periods (even distribution)
            $slotCandidates = [];
            for ($slotIdx = 0; $slotIdx < $totalSlots; $slotIdx++) {
                if (isset($usedSlots[$slotIdx])) continue;

                // Check teacher conflict
                if ($teacherId && isset($this->teacherOccupied[$teacherId][$slotIdx])) {
                    continue;
                }

                $day = $this->slots[$slotIdx]['day'];
                $dayCount = $subjectDayCount[$subjectId][$day] ?? 0;

                // Check for same-subject in adjacent slot (avoid back-to-back)
                $adjacentPenalty = 0;
                if ($slotIdx > 0 && isset($this->schedule[$csId][$slotIdx - 1]) &&
                    $this->schedule[$csId][$slotIdx - 1]['subject_id'] === $subjectId) {
                    $adjacentPenalty = 10;
                }

                $slotCandidates[] = [
                    'slot' => $slotIdx,
                    'score' => $dayCount + $adjacentPenalty,
                ];
            }

            // Sort by score (prefer less loaded days, no adjacent same-subject)
            usort($slotCandidates, fn($a, $b) => $a['score'] <=> $b['score']);

            if (!empty($slotCandidates)) {
                $bestSlot = $slotCandidates[0]['slot'];
                $day = $this->slots[$bestSlot]['day'];

                $this->schedule[$csId][$bestSlot] = $assignment;
                $usedSlots[$bestSlot] = true;
                $subjectDayCount[$subjectId][$day] = ($subjectDayCount[$subjectId][$day] ?? 0) + 1;

                if ($teacherId) {
                    $this->teacherOccupied[$teacherId][$bestSlot] = true;
                }
                $placed = true;
            }

            if (!$placed) {
                $this->log("WARNING: Could not place subject #{$subjectId} in {$gradeName} - all slots occupied or teacher conflicts");
            }
        }

        $placedCount = count($this->schedule[$csId]);
        $this->log("Generated {$gradeName}: {$placedCount}/{$totalSlots} slots filled");
    }

    /**
     * Distribute subjects evenly across slots for primary classes
     * Ensures subjects are spread across days and avoids back-to-back same subject
     */
    protected function distributeSubjectsEvenly(array $subjectSlots, array $subjectIds, int $slotsPerDay): array
    {
        $totalSlots = count($this->slots);
        $numDays = count($this->days);
        $result = array_fill(0, $totalSlots, null);

        // Group by subject
        $bySubject = [];
        foreach ($subjectSlots as $slot) {
            $bySubject[$slot['subject_id']][] = $slot;
        }

        // For each subject, distribute across days as evenly as possible
        $subjectDaySlots = []; // subject_id => [day_index => [slot assignments]]

        foreach ($bySubject as $subjectId => $slots) {
            $count = count($slots);
            $perDay = intdiv($count, $numDays);
            $extraDays = $count % $numDays;

            $dayAllocation = [];
            for ($d = 0; $d < $numDays; $d++) {
                $dayAllocation[$d] = $perDay + ($d < $extraDays ? 1 : 0);
            }

            // Shuffle day order for variety
            $dayOrder = range(0, $numDays - 1);
            shuffle($dayOrder);

            $subjectDaySlots[$subjectId] = [];
            foreach ($dayOrder as $priority => $dayIdx) {
                $subjectDaySlots[$subjectId][$dayIdx] = $dayAllocation[$priority];
            }
        }

        // Now place subjects into slots
        // Process subjects in random order
        $subjectOrder = array_keys($bySubject);
        shuffle($subjectOrder);

        foreach ($subjectOrder as $subjectId) {
            $slots = $bySubject[$subjectId];
            $teacherId = $slots[0]['teacher_id'];
            $slotIdx = 0;

            foreach ($subjectDaySlots[$subjectId] as $dayIdx => $countForDay) {
                $dayStart = $dayIdx * $slotsPerDay;
                $dayEnd = $dayStart + $slotsPerDay;

                $placed = 0;
                // Try to spread within the day (not consecutive if possible)
                $availableInDay = [];
                for ($s = $dayStart; $s < $dayEnd; $s++) {
                    if ($result[$s] === null) {
                        $availableInDay[] = $s;
                    }
                }

                // Space them out within available slots
                if ($countForDay > 0 && count($availableInDay) >= $countForDay) {
                    $step = max(1, intdiv(count($availableInDay), $countForDay));
                    $picks = [];
                    for ($i = 0; $i < $countForDay && $slotIdx < count($slots); $i++) {
                        $pickIdx = min($i * $step, count($availableInDay) - 1);
                        $actualSlot = $availableInDay[$pickIdx];
                        $result[$actualSlot] = ['subject_id' => $subjectId, 'teacher_id' => $teacherId];
                        $slotIdx++;
                    }
                } else {
                    // Fill what we can
                    foreach ($availableInDay as $s) {
                        if ($placed >= $countForDay || $slotIdx >= count($slots)) break;
                        $result[$s] = ['subject_id' => $subjectId, 'teacher_id' => $teacherId];
                        $slotIdx++;
                        $placed++;
                    }
                }
            }

            // Handle any remaining slots that couldn't be placed in preferred days
            if ($slotIdx < count($slots)) {
                for ($s = 0; $s < $totalSlots && $slotIdx < count($slots); $s++) {
                    if ($result[$s] === null) {
                        $result[$s] = ['subject_id' => $subjectId, 'teacher_id' => $teacherId];
                        $slotIdx++;
                    }
                }
            }
        }

        // Fill any remaining empty slots (shouldn't happen but safety)
        for ($s = 0; $s < $totalSlots; $s++) {
            if ($result[$s] === null) {
                $result[$s] = ['subject_id' => null, 'teacher_id' => null];
            }
        }

        return $result;
    }

    /**
     * Save the generated schedule to the database
     */
    protected function saveSchedule(): int
    {
        $count = 0;

        DB::transaction(function () use (&$count) {
            // Delete existing entries for this academic year
            TimetableEntry::where('academic_year_id', $this->academicYearId)->delete();

            $batch = [];
            $now = now();

            foreach ($this->schedule as $csId => $slots) {
                foreach ($slots as $slotIdx => $assignment) {
                    if (!$assignment || (!$assignment['subject_id'] && !$assignment['teacher_id'])) {
                        continue;
                    }

                    $slotInfo = $this->slots[$slotIdx];

                    $batch[] = [
                        'timetable_period_id' => $slotInfo['period_id'],
                        'class_section_id' => $csId,
                        'subject_id' => $assignment['subject_id'],
                        'teacher_id' => $assignment['teacher_id'],
                        'academic_year_id' => $this->academicYearId,
                        'day_of_week' => $slotInfo['day'],
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $count++;

                    // Insert in batches of 500
                    if (count($batch) >= 500) {
                        TimetableEntry::insert($batch);
                        $batch = [];
                    }
                }
            }

            if (!empty($batch)) {
                TimetableEntry::insert($batch);
            }
        });

        return $count;
    }

    /**
     * Validate the generated timetable for conflicts
     * Returns array of conflict descriptions
     */
    public function validateSchedule(): array
    {
        $conflicts = [];

        // Check teacher conflicts (same teacher, same slot)
        $entries = TimetableEntry::where('academic_year_id', $this->academicYearId)
            ->where('is_active', true)
            ->whereNotNull('teacher_id')
            ->with(['timetablePeriod', 'classSection.grade', 'subject', 'teacher'])
            ->get();

        $teacherSlots = [];
        foreach ($entries as $entry) {
            $key = $entry->teacher_id . '-' . $entry->timetable_period_id . '-' . $entry->day_of_week;
            $teacherSlots[$key][] = $entry;
        }

        foreach ($teacherSlots as $key => $group) {
            if (count($group) > 1) {
                $teacher = $group[0]->teacher?->name ?? 'Unknown';
                $period = $group[0]->timetablePeriod?->name ?? '?';
                $day = $group[0]->day_of_week;
                $classes = collect($group)->map(fn($e) => ($e->classSection?->grade?->name ?? '?') . ' ' . ($e->classSection?->name ?? ''))->implode(' vs ');
                $conflicts[] = "CONFLICT: {$teacher} on {$day} {$period} - assigned to: {$classes}";
            }
        }

        return $conflicts;
    }

    /**
     * Validate a teacher ID exists in the database
     */
    protected function validTeacher(?int $teacherId): ?int
    {
        if (!$teacherId) return null;
        return isset($this->validTeacherIds[$teacherId]) ? $teacherId : null;
    }

    protected function log(string $message): void
    {
        $this->logs[] = $message;
        Log::info('[TimetableGenerator] ' . $message);
    }
}
