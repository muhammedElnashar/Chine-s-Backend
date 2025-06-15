<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
        'is_correct',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    public function answer()
    {
        return $this->belongsTo(ExamAnswer::class, 'answer_id');
    }
}

