<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DailyContent;
use App\Http\Controllers\Api\EmailVerify;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\PaidCoursesController;
use App\Http\Controllers\Api\ResetPassword;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserCourseController;
use App\Http\Controllers\Api\VideoAccessController;
use Illuminate\Support\Facades\Route;
//Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
//Reset Password
Route::post('send/otp/reset/password', [ResetPassword::class, 'sendResetPasswordOtp']);
Route::post('verify/otp/reset/password', [ResetPassword::class, 'verifyResetPasswordOtp']);
Route::post('reset/password', [ResetPassword::class, 'resetPassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('email/verify', [EmailVerify::class, 'Verify']);
    Route::post('resend/email/verify', [EmailVerify::class, 'resendVerificationOtp']);
    // User
    Route::patch('update/user/profile', [UserController::class, 'updateUserProfile']);
    Route::delete('delete/user/profile', [UserController::class, 'deleteUserProfile']);
    //Articles, Announcements
    Route::get('articles', [CourseController::class, 'getAllArticles']);
    Route::get('announcements', [CourseController::class, 'getAllAnnouncements']);
    //Courses
    Route::get('free/courses', [CourseController::class, 'getAllFreeCourses']);
    Route::get('paid/courses', [CourseController::class, 'getAllPaidCourses']);
    Route::get('courses/{id}', [UserCourseController::class, 'getCourseDetails']);
    Route::get('user/courses', [UserCourseController::class, 'userCourseList']);
    //Courses and Sections Exams
    Route::get('/exams/{id}', [ExamController::class, 'show']);
    Route::post('/exams/submit', [ExamController::class, 'submit']);
    //Sections and Videos
    Route::get('sections/{id}', [UserCourseController::class, 'getSectionDetails']);
    Route::get('/videos/{id}/presigned-url', [VideoAccessController::class, 'getPresignedUrl']);
    Route::post('/mark/video', [VideoAccessController::class, 'markVideoAsWatched']);
    //Daily Content
    Route::get('daily/questions', [DailyContent::class, 'getDailyTextExercise']);
    Route::get('daily/words', [DailyContent::class, 'getDailyAudioExercise']);
    // Daily Exams
    Route::get('/daily/exams/{id}', [DailyContent::class, 'ShowDailyExercise']);
    Route::post('/daily/exams/submit', [DailyContent::class, 'submitDailyQuiz']);




});
