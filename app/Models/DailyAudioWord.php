<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAudioWord extends Model
{
    protected $fillable = ['daily_exercise_id', 'audio_file', 'word_meaning'];

    public $timestamps = false;

    public function exercise()
    {
        return $this->belongsTo(DailyExercise::class, 'daily_exercise_id');
    }}
