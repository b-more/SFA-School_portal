<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpdResource extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'subject', 'grade',
        'type', 'file_path', 'file_name', 'external_url', 'download_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
