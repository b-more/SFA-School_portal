<?php

namespace App\Services;

use App\Models\ClinicVisit;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Log;

/**
 * Fire-and-forget SMS to a pupil's parent when a clinic visit outcome
 * warrants immediate contact (sent home / referred / sick note issued).
 *
 * Never throws — an SMS gateway hiccup must not roll back a saved visit.
 * Silently no-ops when the visit isn't linked to a student, when no parent
 * phone is on file, or when the number can't be normalised to +260…
 */
class ClinicParentNotifier
{
    public function __construct(private SmsService $sms) {}

    public function notifyIfNeeded(ClinicVisit $visit): void
    {
        if (! $this->shouldNotify($visit)) return;

        try {
            $visit->loadMissing('student.parentGuardian');
            $parent = $visit->student?->parentGuardian;
            $phone  = PhoneNormalizer::normalize($parent?->phone ?? '');
            if (! $phone) {
                Log::channel('scheduler')->info('clinic.parent-sms.skip-no-phone', [
                    'visit_id'    => $visit->id,
                    'student_id'  => $visit->student_id,
                    'raw_phone'   => $parent?->phone,
                ]);
                return;
            }

            $this->sms->send(
                $this->composeMessage($visit),
                $phone,
                'other',
                $visit->id,
                null,
                true
            );
        } catch (\Throwable $e) {
            Log::channel('scheduler')->warning('clinic.parent-sms.exception', [
                'visit_id' => $visit->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function shouldNotify(ClinicVisit $visit): bool
    {
        return in_array($visit->outcome, ['sent_home', 'referred'], true)
            || $visit->sick_note_issued;
    }

    private function composeMessage(ClinicVisit $visit): string
    {
        $name    = $visit->student_name;
        $classes = $visit->grade ? " ({$visit->grade})" : '';

        return match (true) {
            $visit->outcome === 'referred' =>
                "St Francis clinic: {$name}{$classes} has been referred to a health facility today. "
                . 'Please contact the school immediately on +260 972 266 217.',

            $visit->outcome === 'sent_home' =>
                "St Francis clinic: {$name}{$classes} was seen at the school clinic today and sent home. "
                . 'Please come to collect them. Thank you.',

            $visit->sick_note_issued =>
                "St Francis clinic: {$name}{$classes} was seen at the school clinic today "
                . 'and issued a sick note. Kindly monitor them at home this evening.',

            default => "St Francis clinic: {$name}{$classes} was seen at the school clinic today.",
        };
    }
}
