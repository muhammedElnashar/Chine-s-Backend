<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['course_id', 'title', 'position', 'description',];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    public function files()
    {
        return $this->hasMany(LevelFile::class);
    }

    public function exam()
    {
        return $this->hasOne(Exam::class)->whereNotNull('level_id');
    }
}
