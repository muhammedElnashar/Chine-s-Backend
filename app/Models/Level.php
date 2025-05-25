<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['course_id', 'title', 'position', 'price', 'is_free'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_levels')
            ->withTimestamps();
    }
    public function exam()
    {
        return $this->hasOne(LevelExam::class);
    }
}
