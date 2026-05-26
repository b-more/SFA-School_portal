<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'quote',
        'avatar_initials',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $t) {
            if (blank($t->avatar_initials) && filled($t->name)) {
                $t->avatar_initials = Str::upper(
                    collect(preg_split('/\s+/', trim($t->name)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($p) => Str::substr($p, 0, 1))
                        ->implode('')
                );
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}
