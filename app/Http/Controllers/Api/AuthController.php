<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\EmailVerification;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
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
            'type' => 'email_verification',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
        Mail::to($user->email)->send(new EmailVerification($otp));
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login()
    {

    }
}
