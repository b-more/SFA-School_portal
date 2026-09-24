<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentBusAssignment extends Model
{
    protected $fillable = [
        'student_id', 'bus_fare_structure_id', 'monthly_amount',
        'started_at', 'ended_at', 'is_active', 'notes',
    ];

    protected $casts = [
        'monthly_amount' => 'decimal:2',
        'started_at' => 'date',
        'ended_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(BusFareStructure::class, 'bus_fare_structure_id');
    }

    /** Effectively active right now: flag is_active=true and date window covers today. */
    public function scopeCurrentlyActive($q)
    {
        $today = now()->toDateString();
        return $q->where('is_active', true)
            ->where('started_at', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $today);
            });
    }
}
