<?php

use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register',[RegistrationController::class,'register']);
Route::post('/verify',[RegistrationController::class,'verify']);
Route::post('/login',[RegistrationController::class,'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout',[RegistrationController::class,'logout']);
});
