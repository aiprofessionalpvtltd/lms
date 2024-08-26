<?php

use App\Http\Controllers\API\DropdownController;
use App\Http\Controllers\API\GuarantorController;
use App\Http\Controllers\API\LoanApplicationController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [RegisterController::class, 'register']);
Route::post('user/login', [RegisterController::class, 'login']);
 Route::post('user/verify-otp', [RegisterController::class, 'verifyOtp']);

Route::middleware('auth:api')->group(function () {

    Route::post('user/account', [RegisterController::class, 'storeUserBankAccount']);
    Route::post('user/logout', [RegisterController::class, 'logout']);
    Route::get('user/info', [RegisterController::class, 'userInfo']);
    Route::post('user/update', [RegisterController::class, 'updateProfile']);
    Route::post('user/change-password', [RegisterController::class, 'changePassword']);


    Route::get('user/loan-applications', [LoanApplicationController::class, 'getAllData']);
    Route::get('user/user-loan-applications', [LoanApplicationController::class, 'getUserData']);
    Route::post('user/loan-applications', [LoanApplicationController::class, 'store']);
    Route::post('user/loan-applications/documents', [LoanApplicationController::class, 'storeDocuments']);

    // Routes fr dropdowns
    Route::get('dropdown/loan-duration', [DropdownController::class, 'getLoanDuration']);
    Route::get('dropdown/loan-purpose', [DropdownController::class, 'getLoanPurpose']);
    Route::get('dropdown/product-service', [DropdownController::class, 'getProductService']);
    Route::get('dropdown/document-type', [DropdownController::class, 'getDocumentType']);

    Route::post('user/loan-applications/guarantors', [GuarantorController::class, 'store']);
    Route::get('user/loan-applications/guarantors/{id}', [GuarantorController::class, 'show']);
});
