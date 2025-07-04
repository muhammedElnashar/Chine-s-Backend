<?php

namespace App\Models;

 use App\Enum\StatusEnum;
 use App\Enum\UserRoleEnum;
 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\SoftDeletes;
 use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 use Illuminate\Support\Carbon;
 use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'type' => UserRoleEnum::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function purchasedCourses()
    {
        return $this->belongsToMany(Course::class, 'payments')
            ->withTimestamps()
            ->withPivot('paid_at', 'status')
            ->wherePivot('status', StatusEnum::Completed);
    }

    public function hasPurchased($courseId)
    {
        return $this->purchasedCourses()->where('course_id', $courseId)->exists();
    }

    public function isSuperAdmin()
    {
        return $this->role === UserRoleEnum::superAdmin->value;
    }


    public function levelExamAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    public function dailyExerciseAttempts()
    {
        return $this->hasMany(DailyExerciseAttempt::class, 'student_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withTimestamps()
            ->withPivot('purchased_at');
    }
    public function subscribeToCourse(int $courseId, ?Carbon $purchasedAt = null): bool
    {
        if ($this->courses()->where('course_id', $courseId)->exists()) {
            return false;
        }

        $this->courses()->attach($courseId, [
            'purchased_at' => $purchasedAt ?? now(),
        ]);

        return true;
    }

}
