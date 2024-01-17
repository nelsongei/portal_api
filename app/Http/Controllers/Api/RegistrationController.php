<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Register\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    //
    public function register(RegisterRequest $request){
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
            throw $e;
        }
    }
}
