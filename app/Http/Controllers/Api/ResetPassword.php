<?php

namespace App\Http\Controllers\Api;

use App\Enum\OtpTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpResetPasswordRequest;
use App\Http\Requests\VerifyResetPasswordOtpRequest;
use App\Mail\ResetPasswordOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPassword extends Controller
{
    public function sendResetPasswordOtp(SendOtpResetPasswordRequest $request)
    {
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
        Mail::to($user->email)->send(new ResetPasswordOtp($otp));
        return response()->json(['message' => 'Reset Password OTP sent successfully'],200);
    }
    public function verifyResetPasswordOtp(VerifyResetPasswordOtpRequest $request)
    {
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
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        Otp::where('user_id', $user->id)->where('type',OtpTypeEnum::resetPassword)->delete();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
