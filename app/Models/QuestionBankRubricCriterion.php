<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionBankRubricCriterion extends Model
{
    public $timestamps = false;

    protected $table = 'question_bank_rubric_criteria';

    protected $fillable = [
        'question_bank_item_id',
        'criterion',
        'max_marks',
        'position',
    ];

    protected $casts = [
        'max_marks' => 'integer',
        'position' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(QuestionBankItem::class, 'question_bank_item_id');
    }
}
