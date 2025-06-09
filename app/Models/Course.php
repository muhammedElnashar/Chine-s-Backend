<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'type', 'image','price'];
    public function levels()
    {
        return $this->hasMany(Level::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')->withTimestamps()->withPivot('purchased_at');
    }
}

