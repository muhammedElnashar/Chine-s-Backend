<?php

namespace App\Enum;

enum StatusEnum:string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
}
