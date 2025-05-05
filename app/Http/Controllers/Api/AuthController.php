<?php

namespace App\Http\Controllers\Api;

use App\Enum\OtpTypeEnum;
use App\Enum\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\EmailVerificationOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data =$request->all();
        $data['password'] = Hash::make($request->password);
        $data['role']= UserRoleEnum::User;
        $user = User::create($data);
        $otp = random_int(100000, 999999);
        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'type' => OtpTypeEnum::verifyEmail,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
        Mail::to($user->email)->send(new EmailVerificationOtp($otp));
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user= User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Email Or Password , Please try again',
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token
        ]);

    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }
}
