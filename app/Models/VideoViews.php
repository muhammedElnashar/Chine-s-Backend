<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoViews extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
