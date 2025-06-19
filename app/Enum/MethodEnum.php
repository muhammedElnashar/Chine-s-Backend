<?php

namespace App\Enum;

enum MethodEnum:string
{
    case Manual = 'Manual';
    case Stripe = 'Stripe';
    case Paypal = 'Paypal';

}
