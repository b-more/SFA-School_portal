<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UssdSession extends Model
{
    protected $fillable = [
        'session_id', 'msisdn', 'guardian_id', 'shortcode',
        'state', 'data', 'transcript', 'ended', 'last_input_at',
    ];

    protected $casts = [
        'data'          => 'array',
        'transcript'    => 'array',
        'ended'         => 'boolean',
        'last_input_at' => 'datetime',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class, 'guardian_id');
    }

    /** Digits-only msisdn like 260975020473 → "+260 975 020 473". */
    public function getDisplayMsisdnAttribute(): string
    {
        $d = $this->msisdn;
        if (strlen($d) === 12 && str_starts_with($d, '260')) {
            return '+260 ' . substr($d, 3, 3) . ' ' . substr($d, 6, 3) . ' ' . substr($d, 9, 3);
        }
        return '+' . $d;
    }

    public function appendTranscript(string $direction, string $text): void
    {
        $t = $this->transcript ?? [];
        $t[] = [
            'ts'   => now()->toIso8601String(),
            'dir'  => $direction, // 'in' | 'out'
            'text' => mb_substr($text, 0, 500),
        ];
        $this->transcript = $t;
    }
}
