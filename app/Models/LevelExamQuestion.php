<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelExamQuestion extends Model
{
    protected $fillable = ['level_exam_id', 'question_text'];
    public function exam()
    {
        return $this->belongsTo(LevelExam::class, 'level_exam_id');
    }

    public function answers()
    {
        return $this->hasMany(LevelExamAnswer::class, 'question_id');
    }
}
