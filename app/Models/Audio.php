<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $fillable = [
        'level_id',
        'title',
        'audio_url',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

}
