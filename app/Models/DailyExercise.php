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

}
