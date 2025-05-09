<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseTextQuestion extends Model
{
    public function answers()
    {
        return $this->morphMany(Answer::class, 'questionable');
    }
}
