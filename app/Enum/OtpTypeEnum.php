<?php

namespace App\Enum;

enum OtpTypeEnum:string
{
    case resetPassword = 'reset_password';
    case verifyEmail = 'verify_email';
}
