<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseAnswer extends Model
{

    public function questionable()
    {
        return $this->morphTo();
    }
}
