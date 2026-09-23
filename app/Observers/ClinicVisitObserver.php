<?php

namespace App\Observers;

use App\Models\ClinicVisit;
use Illuminate\Support\Facades\DB;

/**
 * Writes one audit_logs row per meaningful change to a clinic visit.
 *
 * A clinic visit is a medical record — a corrected outcome ('sent-home' →
 * 'referred'), a rewritten complaint note, or a fixed pupil identity all
 * change what the record says. Auto-touched timestamps do not.
 *
 * The trail lives in the same audit_logs table used for authentication,
 * keyed polymorphically by auditable_type = ClinicVisit::class, so the
 * existing viewer at /admin/login-activity is one JOIN away from becoming
 * a clinic audit viewer if we later want one.
 */
class ClinicVisitObserver
{
    /**
     * Fields whose diff we actually record. Timestamps and IDs of related
     * side-tables are not clinically meaningful on their own.
     */
    private const TRACKED = [
        'visit_date',
        'student_id',
        'student_name',
        'grade',
        'grade_level',
        'complaint_notes',
        'sick_note_issued',
        'outcome',
        'recorded_by',
        'needs_review',
    ];

    public function created(ClinicVisit $v): void
    {
        $this->write('created', $v, [], $v->only(self::TRACKED));
    }

    public function updated(ClinicVisit $v): void
    {
        $dirty = array_intersect_key($v->getChanges(), array_flip(self::TRACKED));
        if (empty($dirty)) return; // only timestamps changed — not worth a row

        $old = array_intersect_key($v->getOriginal(), $dirty);
        $this->write('updated', $v, $old, $dirty);
    }

    public function deleted(ClinicVisit $v): void
    {
        $this->write('deleted', $v, $v->getOriginal(), []);
    }

    private function write(string $event, ClinicVisit $v, array $old, array $new): void
    {
        DB::table('audit_logs')->insert([
            'auditable_type' => ClinicVisit::class,
            'auditable_id'   => $v->id,
            'event'          => 'clinic_visit.' . $event,
            'old_values'     => empty($old) ? null : json_encode($old, JSON_UNESCAPED_UNICODE),
            'new_values'     => empty($new) ? null : json_encode($new, JSON_UNESCAPED_UNICODE),
            'user_id'        => auth()->id(),
            'ip_address'     => request()->ip() ?? null,
            'user_agent'     => request()->userAgent() ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
