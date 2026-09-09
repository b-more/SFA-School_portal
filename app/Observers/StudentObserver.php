<?php

namespace App\Observers;

use App\Models\Student;
use App\Notifications\NewStudentRegistered;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Log;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        AdminNotificationService::notifyAdmins(new NewStudentRegistered($student));
    }

    /**
     * When a pupil moves out of `active` (transferred / graduated / inactive),
     * free their class slot so any status-blind roster stops counting them.
     * The record itself stays for reporting and history.
     *
     * Fires on `updating` (before the row is saved) so the null goes down in
     * the same UPDATE — no second write, no observer loop.
     */
    public function updating(Student $student): void
    {
        if (! $student->isDirty('enrollment_status')) {
            return;
        }

        $newStatus = $student->enrollment_status;
        if ($newStatus === 'active' || $newStatus === null) {
            return;
        }

        if ($student->class_section_id !== null) {
            Log::info('StudentObserver: freeing class slot on non-active status change', [
                'student_id'     => $student->id,
                'name'           => $student->name,
                'from_status'    => $student->getOriginal('enrollment_status'),
                'to_status'      => $newStatus,
                'freed_class_id' => $student->class_section_id,
            ]);
            $student->class_section_id = null;
        }
    }
}
