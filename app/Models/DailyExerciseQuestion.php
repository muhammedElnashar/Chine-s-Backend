<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyExerciseQuestion extends Model
{
    protected $fillable = ['exercise_id', 'question_type', 'question_text', 'question_media_url','explanation'];

    public function exercise()
    {
        return $this->belongsTo(DailyExercise::class, 'exercise_id');
    }

    public function answers()
    {
        return $this->hasMany(DailyExerciseQuestionAnswer::class,'question_id');
    }

}
