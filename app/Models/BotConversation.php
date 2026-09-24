<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per inbound or outbound message on the WhatsApp bot.
 * Written by the bot via POST /api/bot/log; read by BotConversationResource
 * in Filament to render a WhatsApp-style thread for each parent phone.
 */
class BotConversation extends Model
{
    protected $fillable = [
        'phone',
        'direction',
        'kind',
        'content',
        'meta',
        'flow_state',
        'guardian_id',
        'message_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class, 'guardian_id');
    }

    /**
     * Digits-only normaliser so the bot and Filament agree on the phone key.
     * Accepts +260…, 260…, 0977…, etc.; returns 260XXXXXXXXX or null.
     */
    public static function normalisePhone(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }
        $d = preg_replace('/\D+/', '', $raw);
        if ($d === '' || $d === null) {
            return null;
        }
        if (str_starts_with($d, '00')) {
            $d = ltrim($d, '0');
        }
        if (strlen($d) === 12 && str_starts_with($d, '260')) {
            return $d;
        }
        if (strlen($d) === 10 && str_starts_with($d, '0')) {
            return '260' . substr($d, 1);
        }
        if (strlen($d) === 9) {
            return '260' . $d;
        }
        return $d;
    }

    /**
     * Human-friendly display for a phone: "+260 977 020 473".
     */
    public function getDisplayPhoneAttribute(): string
    {
        $d = $this->phone;
        if (strlen($d) === 12 && str_starts_with($d, '260')) {
            return '+260 ' . substr($d, 3, 3) . ' ' . substr($d, 6, 3) . ' ' . substr($d, 9, 3);
        }
        return '+' . $d;
    }
}
