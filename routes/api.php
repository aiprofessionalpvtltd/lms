<?php

use App\Http\Controllers\API\RecoveryController;
use App\Http\Controllers\API\DropdownController;
use App\Http\Controllers\API\GuarantorController;
use App\Http\Controllers\API\LoanApplicationController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [RegisterController::class, 'register']);
Route::post('user/login', [RegisterController::class, 'login']);
Route::post('user/verify-otp', [RegisterController::class, 'verifyOtp']);
Route::post('user/forgot-password', [RegisterController::class, 'forgotPassword']);
Route::post('user/verify-otp-reset-password', [RegisterController::class, 'verifyOtpAndResetPassword']);
Route::post('user/reset-password', [RegisterController::class, 'resetPassword']);


Route::get('dropdown/loan-duration', [DropdownController::class, 'getLoanDuration']);
Route::get('dropdown/loan-purpose', [DropdownController::class, 'getLoanPurpose']);
Route::get('dropdown/product-service', [DropdownController::class, 'getProductService']);
Route::get('dropdown/document-type', [DropdownController::class, 'getDocumentType']);
Route::get('dropdown/genders', [DropdownController::class, 'getGenders']);
Route::get('dropdown/marital-statuses', [DropdownController::class, 'getMaritalStatuses']);
Route::get('dropdown/nationalities', [DropdownController::class, 'getNationalities']);
Route::get('dropdown/incomeSource', [DropdownController::class, 'getIncomeSource']);
Route::get('dropdown/employmentStatus', [DropdownController::class, 'getEmploymentStatus']);
Route::get('dropdown/educations', [DropdownController::class, 'getEducation']);
Route::get('dropdown/relationships', [DropdownController::class, 'getRelationShip']);
Route::get('dropdown/getJobTitle', [DropdownController::class, 'getJobTitle']);
Route::get('dropdown/getResidenceTypes', [DropdownController::class, 'getResidenceTypes']);
Route::get('dropdown/getResidenceDuration', [DropdownController::class, 'getResidenceDuration']);
Route::get('dropdown/getExistingLoans', [DropdownController::class, 'getExistingLoans']);
Route::get('dropdown/getProvinceByCountry', [DropdownController::class, 'getProvinceByCountry']);
Route::get('dropdown/getDistrictByProvince', [DropdownController::class, 'getDistrictByProvince']);
Route::get('dropdown/getCityByProvince', [DropdownController::class, 'getCityByProvince']);
Route::get('dropdown/banks', [DropdownController::class, 'getBanks']);


Route::post('user/loan-calculator', [LoanApplicationController::class, 'calculateLoan']);


Route::middleware('auth:api')->group(function () {

    Route::post('user/logout', [RegisterController::class, 'logout']);
    Route::post('user/change-password', [RegisterController::class, 'changePassword']);

    Route::get('user/info', [UserController::class, 'userInfo']);
    Route::post('user/account', [UserController::class, 'storeUserBankAccount']);
    Route::post('user/update', [UserController::class, 'updateProfile']);
    Route::post('user/employment', [UserController::class, 'storeUserEmployment']);
    Route::post('user/store-profile', [UserController::class, 'storeProfile']);
    Route::post('user/family-dependents', [UserController::class, 'storeFamilyDependent']);
    Route::post('user/user-guarantor', [UserController::class, 'storeUserGuarantor']);
    Route::post('user/user-education', [UserController::class, 'storeUserEducation']);
    Route::get('user/tracking', [UserController::class, 'getTracking']);


    Route::get('user/loan-applications', [LoanApplicationController::class, 'getAllData']);
    Route::get('user/user-loan-applications', [LoanApplicationController::class, 'getUserData']);
    Route::get('user/loan-application/tracking', [LoanApplicationController::class, 'getApplicationTracking']);
    Route::post('user/loan-applications', [LoanApplicationController::class, 'store']);
    Route::post('user/loan-applications/documents', [LoanApplicationController::class, 'storeDocuments']);
    Route::post('user/loan-applications/calculation', [LoanApplicationController::class, 'storeAmountAndDurationAfterCalculation']);
    Route::get('user/loan-applications/checkEligibility', [LoanApplicationController::class, 'checkEligibility']);
    Route::post('user/loan-applications/storeCalculation', [LoanApplicationController::class, 'storeCalculation']);

    Route::get('user/get-dashboard-data', [LoanApplicationController::class, 'getDashboardData']);
    Route::get('user/installments/upcoming', [LoanApplicationController::class, 'getUpcomingInstallments']);
    Route::get('user/installments/history', [LoanApplicationController::class, 'getInstallmentHistory']);
    Route::get('user/installments/all', [LoanApplicationController::class, 'getAllInstallments']);
    Route::get('user/installments/late-fee-summary', [LoanApplicationController::class, 'getLateFeeSummary']);
    // Routes fr dropdowns

    Route::post('user/loan-applications/guarantors', [GuarantorController::class, 'store']);
    Route::get('user/loan-applications/guarantors/{id}', [GuarantorController::class, 'show']);

    Route::get('dropdown/getProducts', [DropdownController::class, 'getProducts']);


    Route::post('recovery/installment', [RecoveryController::class, 'instalmentRecovery']);



});
