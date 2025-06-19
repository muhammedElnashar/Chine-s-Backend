<?php

namespace App\Models;

use App\Enum\DailyExerciseTypeEnum;
use Illuminate\Database\Eloquent\Model;

class DailyExercise extends Model
{
    protected $fillable = ['date','type', 'title', 'description'];
    protected $casts = [
        'type' => DailyExerciseTypeEnum::class,
    ];

    public function questions()
    {
        return $this->hasMany(DailyExerciseQuestion::class, 'exercise_id');
    }
    public function attempts()
    {
        return $this->hasMany(DailyExerciseAttempt::class);
    }
    public function audioWords()
    {
        return $this->hasMany(DailyAudioWord::class, 'exercise_id');
    }

}
