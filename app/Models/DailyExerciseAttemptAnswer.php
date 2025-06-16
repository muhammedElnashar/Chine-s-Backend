<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyExerciseAttemptAnswer extends Model
{
    protected $fillable = ['attempt_id', 'question_id', 'answer_id', 'is_correct'];

    public function attempt()
    {
        return $this->belongsTo(DailyExerciseAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(DailyExerciseQuestion::class, 'question_id');
    }

    public function answer()
    {
        return $this->belongsTo(DailyExerciseQuestionAnswer::class, 'answer_id');
    }
}

