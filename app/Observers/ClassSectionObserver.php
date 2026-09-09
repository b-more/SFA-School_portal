<?php

namespace App\Observers;

use App\Models\ClassSection;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Keeps the two sides of the class-teacher pointer in sync.
 *
 *   class_sections.class_teacher_id     ← the authoritative pointer, set by
 *                                         the admin on the Class Section page.
 *   teachers.class_section_id           ← the back-pointer used by teacher
 *   teachers.is_class_teacher             dashboards and the teacher app.
 *
 * Nothing was keeping these aligned before, so admins editing the two sides
 * separately drifted them apart — the symptom was a teacher's own screen
 * saying "no class assigned" while the class list clearly showed them.
 */
class ClassSectionObserver
{
    public function updated(ClassSection $section): void
    {
        if (! $section->wasChanged('class_teacher_id')) {
            return;
        }

        $previousTeacherId = $section->getOriginal('class_teacher_id');
        $newTeacherId      = $section->class_teacher_id;

        DB::transaction(function () use ($section, $previousTeacherId, $newTeacherId) {
            // Clear the previous teacher's back-pointer if they were the
            // only one pointing here as class-teacher.
            if ($previousTeacherId && $previousTeacherId !== $newTeacherId) {
                Teacher::where('id', $previousTeacherId)
                    ->where('class_section_id', $section->id)
                    ->update([
                        'class_section_id' => null,
                        'is_class_teacher' => 0,
                    ]);
            }

            // Stamp the new teacher's back-pointer.
            if ($newTeacherId) {
                Teacher::where('id', $newTeacherId)->update([
                    'class_section_id' => $section->id,
                    'is_class_teacher' => 1,
                ]);
            }
        });

        Log::info('ClassSectionObserver: synced teacher back-pointer', [
            'class_section_id'  => $section->id,
            'previous_teacher'  => $previousTeacherId,
            'new_teacher'       => $newTeacherId,
        ]);
    }

    public function created(ClassSection $section): void
    {
        // A brand-new class-section created with a teacher already chosen
        // also needs the back-pointer stamped.
        if ($section->class_teacher_id) {
            Teacher::where('id', $section->class_teacher_id)->update([
                'class_section_id' => $section->id,
                'is_class_teacher' => 1,
            ]);
        }
    }

    public function deleting(ClassSection $section): void
    {
        // On class-section delete, clear the back-pointer so a teacher
        // isn't left claiming class-teachership of a phantom class.
        if ($section->class_teacher_id) {
            Teacher::where('id', $section->class_teacher_id)
                ->where('class_section_id', $section->id)
                ->update([
                    'class_section_id' => null,
                    'is_class_teacher' => 0,
                ]);
        }
    }
}
