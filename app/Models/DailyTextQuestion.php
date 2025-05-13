<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTextQuestion extends Model
{
    protected $fillable = ['question_text', 'daily_exercise_id'];

    public function exercise()
    {
        return $this->belongsTo(DailyExercise::class, 'daily_exercise_id');
    }

    public function answers()
    {
        return $this->hasMany(DailyTextQuestionAnswer::class);
    }

}
