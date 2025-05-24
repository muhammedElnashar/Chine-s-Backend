<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelExamAttempt extends Model
{
    protected $fillable = [
        'level_exam_id',
        'student_id',
        'score',
    ];

    public function exam()
    {
        return $this->belongsTo(LevelExam::class, 'level_exam_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(LevelExamAttemptAnswer::class, 'attempt_id');
    }
}

