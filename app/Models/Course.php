<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'type', 'image'];
    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
