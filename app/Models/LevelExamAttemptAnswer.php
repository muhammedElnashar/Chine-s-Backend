<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelExamAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
        'is_correct',
    ];

    public function attempt()
    {
        return $this->belongsTo(LevelExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(LevelExamQuestion::class, 'question_id');
    }

    public function answer()
    {
        return $this->belongsTo(LevelExamAnswer::class, 'answer_id');
    }
}

