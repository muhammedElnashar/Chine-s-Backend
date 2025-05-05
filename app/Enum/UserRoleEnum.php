<?php

namespace App\Enum;

enum UserRoleEnum: string {
    case superAdmin = 'super_admin';
    case Admin = 'admin';
    case User = 'user';
}
