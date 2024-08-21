<?php

use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [RegisterController::class, 'register']);
Route::post('user/login', [RegisterController::class, 'login']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
