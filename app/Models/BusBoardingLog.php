<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusBoardingLog extends Model
{
    use HasFactory;

    public const STATUS_BOARDED = 'boarded';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_NO_SHOW = 'no_show';

    public const TRIP_TO_SCHOOL = 'to_school';
    public const TRIP_FROM_SCHOOL = 'from_school';

    protected $fillable = [
        'bus_fare_structure_id',
        'student_id',
        'date',
        'trip',
        'status',
        'notes',
        'recorded_by_user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function busFareStructure(): BelongsTo
    {
        return $this->belongsTo(BusFareStructure::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_BOARDED => 'Boarded',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_NO_SHOW => 'No-show',
        ];
    }

    public static function trips(): array
    {
        return [
            self::TRIP_TO_SCHOOL => 'To School',
            self::TRIP_FROM_SCHOOL => 'From School',
        ];
    }
}
