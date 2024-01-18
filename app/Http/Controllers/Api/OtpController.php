<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    //
    public function generateOtp($email)
    {
        $user = User::where('email', $email)->first();
        $verificationCode = Otp::where('user_id', $user->id)->latest()->first();
        $now = Carbon::now();
        if ($verificationCode && $now->isBefore($verificationCode->expires_at)) {
            return $verificationCode;
        }
        return Otp::create([
            'user_id' => $user->id,
            'otp' => random_int(1234, 9999),
            'expires_at' => Carbon::now()->addMinutes(2),
        ]);
    }
    public function verify($email)
    {
        $user = User::where('email', $email)->first();
        $verificationCode = $this->generateOtp($email);
        $otp = $verificationCode->otp;
        return response()->json(['user'=>$user,'otp'=>$otp,'messege'=>'OTP Generated SUccessfully, Check Your Email'], 200);
    }
}
