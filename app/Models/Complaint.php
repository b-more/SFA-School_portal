<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * School grievance / complaint record — parents or staff logging concerns
 * about behavioural, academic, or admin issues. This is the pre-existing
 * `complaints` table and its Filament resource (ComplaintResource) — do not
 * confuse with the medical `clinic_complaints` table which is a separate
 * lookup (ClinicComplaint model).
 */
class Complaint extends Model
{
    protected $table = 'complaints';

    protected $fillable = [
        'complainant_name', 'phone', 'email',
        'complaint_type', 'subject', 'description',
        'priority', 'status', 'resolution',
        'related_student_id', 'logged_by', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'related_student_id');
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
