<?php

namespace App\Http\Controllers\Api;

use App\Enum\OtpTypeEnum;
use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpResetPasswordRequest;
use App\Http\Requests\VerifyResetPasswordOtpRequest;
use App\Mail\ResetPasswordOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResetPassword extends Controller
{
    public function sendResetPasswordOtp(SendOtpResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $otp = rand(100000, 999999);

            Otp::updateOrCreate(
                ['user_id' => $user->id, 'type' => OtpTypeEnum::resetPassword],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'attempts' => 0
                ]
            );
            try {
                Mail::to($user->email)->send(new ResetPasswordOtp($otp));
            } catch (\Throwable $mailError) {
                Log::error('Failed Send OTP: ' . $mailError->getMessage());
            }
            return response()->json(['message' => 'Reset Password OTP sent successfully'], 200);
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);

        }
    }

    public function verifyResetPasswordOtp(VerifyResetPasswordOtpRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $otpRecord = Otp::where('user_id', $user->id)
                ->where('type', OtpTypeEnum::resetPassword)
                ->first();

            if ($otpRecord->otp != $request->otp) {
                $otpRecord->increment('attempts');
                if ($otpRecord->attempts >= 5) {
                    $otpRecord->delete();
                    return response()->json(['message' => 'Your OTP has expired. Please request a new one.'], 400);
                }
                return response()->json(['message' => 'Invalid OTP'], 400);
            }
            if (Carbon::now()->isAfter($otpRecord->expires_at)) {
                $otpRecord->delete();
                return response()->json(['message' => 'OTP has expired'], 400);
            }

            return response()->json(['message' => 'OTP verified successfully'], 200);

        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->update(['password' => Hash::make($request->password)]);
            Otp::where('user_id', $user->id)->where('type', OtpTypeEnum::resetPassword)->delete();

            return response()->json(['message' => 'Password reset successfully'], 200);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }
}
