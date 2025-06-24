<?php

namespace App\Models;

use App\Enum\CourseTypeEnum;
use App\Enum\MethodEnum;
use App\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'type', 'image','price'];
    protected $casts = [
        'type' => CourseTypeEnum::class,
        'method' => MethodEnum::class,
    ];

    public function levels()
    {
        return $this->hasMany(Level::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')->withTimestamps()->withPivot('purchased_at');
    }
    public function paidUsers()
    {
        return $this->hasMany(Payment::class)
            ->where('status', StatusEnum::Completed);
    }
    public function exam()
    {
        return $this->hasOne(Exam::class)->whereNull('level_id');
    }
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

}

