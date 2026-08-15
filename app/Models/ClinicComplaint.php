<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Medical complaint (headache / stomach ache / fever …) recorded on a clinic
 * visit. Distinct from the unrelated `complaints` table used for the school's
 * general grievance system — that one is administrative, this one is clinical.
 */
class ClinicComplaint extends Model
{
    protected $table = 'clinic_complaints';

    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (self $c) {
            if (empty($c->slug)) $c->slug = Str::slug($c->name);
        });
    }

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(ClinicVisit::class, 'clinic_visit_clinic_complaint');
    }
}
