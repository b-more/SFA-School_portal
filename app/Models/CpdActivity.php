<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpdActivity extends Model
{
    protected $fillable = [
        'user_id', 'title', 'type', 'provider', 'start_date', 'end_date',
        'hours', 'description', 'reflection', 'key_learnings',
        'certificate_file', 'status', 'academic_year', 'term',
        'is_mandatory', 'approval_status', 'approved_by', 'approved_at',
        'approval_remarks', 'points', 'goal_id', 'observation_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'hours' => 'decimal:1',
        'points' => 'decimal:1',
        'is_mandatory' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function goal(): BelongsTo { return $this->belongsTo(CpdGoal::class, 'goal_id'); }
    public function observation(): BelongsTo { return $this->belongsTo(CpdObservation::class, 'observation_id'); }
}
