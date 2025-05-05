<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerify;
use App\Http\Controllers\Api\ResetPassword;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send/otp/reset/password', [ResetPassword::class, 'sendResetPasswordOtp']);
Route::post('verify/otp/reset/password', [ResetPassword::class, 'verifyResetPasswordOtp']);
Route::post('reset/password', [ResetPassword::class, 'resetPassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('email/verify', [EmailVerify::class, 'Verify']);
    Route::post('resend/email/verify', [EmailVerify::class, 'resendVerificationOtp']);
});
