<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['level_id', 'title', 'video_url','duration'];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function views()
    {
        return $this->hasMany(VideoViews::class);
    }

}
