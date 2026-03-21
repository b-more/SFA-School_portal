<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'filters',
        'recipient_scope',
        'total_recipients',
        'sent_count',
        'failed_count',
        'total_cost',
        'status',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'filters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'broadcast_reads')
            ->withPivot('read_at');
    }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function markAsRead(int $userId): void
    {
        if (!$this->isReadBy($userId)) {
            $this->reads()->attach($userId, ['read_at' => now()]);
        }
    }

    /**
     * Check if this broadcast is visible to a given user based on recipient_scope
     */
    public function isVisibleToStaff(): bool
    {
        return in_array($this->recipient_scope, ['all_staff', 'teachers']);
    }

    /**
     * Get unread count for a user
     */
    public static function unreadCountFor(int $userId): int
    {
        return static::where('status', 'completed')
            ->whereIn('recipient_scope', ['all_staff', 'teachers'])
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();
    }

    public function getCompletionPercentageAttribute(): int
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return round(($this->sent_count + $this->failed_count) / $this->total_recipients * 100);
    }
}
