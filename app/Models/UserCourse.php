<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    protected $fillable = ['user_id', 'course_id', 'purchased_at'];
    public $timestamps = true;

}
