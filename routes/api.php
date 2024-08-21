<?php

use App\Http\Controllers\API\LoanApplicationController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [RegisterController::class, 'register']);
Route::post('user/login', [RegisterController::class, 'login']);
 Route::post('user/verify-otp', [RegisterController::class, 'verifyOtp']);

Route::middleware('auth:api')->group(function () {

Route::get('user/loan-applications', [LoanApplicationController::class, 'getAllData']);
Route::get('user/user-loan-applications', [LoanApplicationController::class, 'getUserData']);
Route::post('user/loan-applications', [LoanApplicationController::class, 'store']);
});
