<?php

namespace App\Models;

use App\Enum\OtpTypeEnum;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['user_id', 'otp', 'expires_at','attempts', 'type'];
    protected $casts = [
        'type' => OtpTypeEnum::class,
    ];
}
