<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTextQuestion extends Model
{
    protected $fillable = ['daily_exercise_id', 'question_type', 'question_text', 'question_media_url','explanation'];

    public function exercise()
    {
        return $this->belongsTo(DailyExercise::class, 'daily_exercise_id');
    }

    public function answers()
    {
        return $this->hasMany(DailyTextQuestionAnswer::class);
    }

}
