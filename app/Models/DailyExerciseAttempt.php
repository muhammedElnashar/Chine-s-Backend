<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyExerciseAttempt extends Model
{
    protected $fillable = ['exercise_id', 'student_id', 'score'];

    public function exercise()
    {
        return $this->belongsTo(DailyExercise::class, 'exercise_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(DailyExerciseAttemptAnswer::class, 'attempt_id');
    }
}

