<?php

namespace App\Models;

use App\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'amount',
        'status',
        'payment_method',
        'paid_at',
    ];
    protected $casts = [
        'type' => StatusEnum::class,
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the course associated with the payment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
