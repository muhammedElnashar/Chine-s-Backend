<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['course_id','level_id', 'title', 'description'];
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
    public function studentAttempt()
    {
        return $this->hasOne(ExamAttempt::class)->where('student_id', auth()->id());
    }

}

