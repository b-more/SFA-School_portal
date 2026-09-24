<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentGuardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'nrc',
        'nationality',
        'phone',
        'alternate_phone',
        'relationship',
        'occupation',
        'address',
        'user_id',
        'role_id',
    ];

    /**
     * When the phone is edited on a parent record, push the new value through
     * to the linked user account so they can still log in. We sync both:
     *   - users.phone   (the raw value as stored on the parent)
     *   - users.username (normalised to the 260… international form, since
     *                     parents log in with their phone number per school
     *                     policy). If another user has already taken the new
     *                     normalised phone as their username, we skip the
     *                     username swap and surface the collision in the
     *                     Laravel log so an admin can resolve it manually.
     */
    protected static function booted(): void
    {
        static::saved(function (ParentGuardian $parent) {
            if (! $parent->wasChanged('phone') || ! $parent->user_id) {
                return;
            }

            $user = User::find($parent->user_id);
            if (! $user) {
                return;
            }

            $normalised = self::normalisePhoneToInternational($parent->phone);

            $updates = ['phone' => $parent->phone];

            if ($normalised) {
                $taken = User::where('username', $normalised)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if (! $taken) {
                    $updates['username'] = $normalised;
                } else {
                    \Illuminate\Support\Facades\Log::warning(
                        'ParentGuardian phone edit: username collision; left user.username unchanged.',
                        [
                            'parent_guardian_id' => $parent->id,
                            'user_id' => $user->id,
                            'attempted_username' => $normalised,
                        ]
                    );
                }
            }

            $user->forceFill($updates)->saveQuietly();
        });
    }

    /**
     * '+260975020473' / '0975020473' / '260975020473' → '260975020473'.
     * Returns null on garbage input.
     */
    protected static function normalisePhoneToInternational(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $raw);
        if ($digits === '' || $digits === null) {
            return null;
        }
        if (str_starts_with($digits, '260')) {
            return $digits;
        }
        if (str_starts_with($digits, '0')) {
            return '260' . substr($digits, 1);
        }
        if (strlen($digits) >= 9 && strlen($digits) <= 10) {
            return '260' . ltrim($digits, '0');
        }
        return $digits;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }
    public function getFullPhoneAttribute(): string
    {
        return $this->phone;
    }
}
