<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelExam extends Model
{
    protected $fillable = ['level_id', 'title', 'description'];
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function questions()
    {
        return $this->hasMany(LevelExamQuestion::class);
    }
    public function attempts()
    {
        return $this->hasMany(LevelExamAttempt::class);
    }
}
