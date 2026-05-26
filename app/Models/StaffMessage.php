<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffMessage extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
