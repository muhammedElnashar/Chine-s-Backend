<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelFile extends Model
{
    protected $fillable = [
        'level_id',
        'name',
        'path',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
