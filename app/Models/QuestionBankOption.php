<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionBankOption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'question_bank_item_id',
        'option_text',
        'is_correct',
        'position',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'position' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(QuestionBankItem::class, 'question_bank_item_id');
    }
}
