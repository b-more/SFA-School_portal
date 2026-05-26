<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'bus_fare_structure_id',
        'academic_year_id',
        'term_id',
        'month',
        'year',
        'amount',
        'amount_paid',
        'balance',
        'payment_status',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'year' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (BusPayment $busPayment) {
            $busPayment->balance = $busPayment->amount - ($busPayment->amount_paid ?? 0);

            if ($busPayment->balance <= 0) {
                $busPayment->payment_status = 'paid';
            } elseif (($busPayment->amount_paid ?? 0) > 0) {
                $busPayment->payment_status = 'partial';
            } else {
                $busPayment->payment_status = 'unpaid';
            }
        });

        static::updating(function (BusPayment $busPayment) {
            $busPayment->balance = $busPayment->amount - ($busPayment->amount_paid ?? 0);

            if ($busPayment->balance <= 0) {
                $busPayment->payment_status = 'paid';
            } elseif (($busPayment->amount_paid ?? 0) > 0) {
                $busPayment->payment_status = 'partial';
            } else {
                $busPayment->payment_status = 'unpaid';
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function busFareStructure(): BelongsTo
    {
        return $this->belongsTo(BusFareStructure::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Effective expiry date of this payment's bus pass.
     *
     * Monthly plan  → last day of the paid month + year.
     * Per-term plan → term.end_date.
     *
     * Returns null if data is incomplete (e.g. a monthly row with no month name).
     */
    public function getExpiresAtAttribute(): ?\Illuminate\Support\Carbon
    {
        $plan = $this->busFareStructure?->payment_plan;

        if ($plan === 'monthly') {
            if (! $this->month || ! $this->year) {
                return null;
            }

            try {
                return \Illuminate\Support\Carbon::parse("{$this->month} 1 {$this->year}")->endOfMonth();
            } catch (\Throwable $e) {
                return null;
            }
        }

        if ($plan === 'per_term') {
            return $this->term?->end_date
                ? \Illuminate\Support\Carbon::parse($this->term->end_date)->endOfDay()
                : null;
        }

        return null;
    }

    /**
     * Update payment status based on balance
     */
    public function updatePaymentStatus(): void
    {
        if ($this->balance <= 0) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }
}
