<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    protected $fillable=[
        'user_id',
        'level_id',
        "purchased_at"
    ];
    public function purchasedLevels()
    {
        return $this->belongsToMany(Level::class)->withTimestamps()->withPivot('purchased_at');
    }

    public function hasPurchasedLevel($levelId)
    {
        return $this->purchasedLevels()->where('level_id', $levelId)->exists();
    }

}
