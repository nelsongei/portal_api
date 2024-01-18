<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Login\LoginRequest;
use App\Http\Requests\Register\RegisterRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    //
    public function register(RegisterRequest $request)
    {
        try {
            $response = new ResponseController();
            $otp = new OtpController();
            DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            DB::commit();
            return $otp->verify($request->email);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['errors' => $e->getMessage()]);
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'Register first', 'status' => 0]);
            }
            $now = Carbon::now();
            $verify = new OtpController();
            return $verify->verify($request->email);
            // if ($otp) {
            //     return response()->json(['message' => 'OTP Code Has Been Send To Your Email', 'otp' => $otp]);
            // }
        } catch (\Exception $e) {
        }
    }
    public function verify(Request $request)
    {
        //dd($request->all());
        $now = Carbon::now();
        $validate = Validator::make($request->all(), [
            'otp' => 'required',
        ], [
            'otp.required' => 'OTP number is required',
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors()->all());
        } else {
            $user = User::where('email', $request->email)->first();
            //dd()
            $verificationCode = Otp::where('user_id', $user->id)->where('otp', $request->otp)->latest()->first();
            if (!$verificationCode) {
                return response()->json('Enter Correct OTP', 402);
            } elseif ($verificationCode && $now->isAfter($verificationCode->expires_at)) {
                return response()->json('Your OTP Has Expired', 401);
            } else {
                Auth::login($user);
                $device_name = 'portalapi';
                $success['token'] =  $user->createToken($device_name)->plainTextToken;
                $success['user'] =  $user;
                return response()->json($success, 200);
            }
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        $response = [
            'success' => true,
            'message' => 'Logged out Successfully'
        ];
        return response()->json($response, 200);
    }
}
