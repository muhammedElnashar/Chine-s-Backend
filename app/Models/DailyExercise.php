<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyExercise extends Model
{
    protected $fillable = ['date'];

    public function questions()
    {
        return $this->hasMany(DailyTextQuestion::class);
    }
    public function attempts()
    {
        return $this->hasMany(DailyExerciseAttempt::class);
    }
    public function audioWords()
    {
        return $this->hasMany(DailyAudioWord::class);
    }

}
