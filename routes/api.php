<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerify;
use App\Http\Controllers\Api\ResetPassword;
use App\Http\Controllers\Api\UserController;
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
    //Email Verification
    Route::post('email/verify', [EmailVerify::class, 'Verify']);
    Route::post('resend/email/verify', [EmailVerify::class, 'resendVerificationOtp']);
    // User
    Route::patch('update/user/profile', [UserController::class, 'updateUserProfile']);
    Route::delete('delete/user/profile', [UserController::class, 'deleteUserProfile']);

});
