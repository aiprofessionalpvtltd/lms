<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\Dashboard\SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstallmentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RecoveryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\API\LoanApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/get-province-by-country', [AjaxController::class, 'getProvinceByCountryAjax']);
Route::get('/get-district-by-province', [AjaxController::class, 'getDistrictByProvinceAjax']);
Route::get('/get-city-by-province', [AjaxController::class, 'getCityByProvinceAjax']);

Route::middleware(['auth'])->group(function () {

    Route::get('our-dashboard', [SuperAdminDashboardController::class, 'index'])->name('our-dashboard');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('update-password', [AdminController::class, 'updatePassword'])->name('update-password');
    Route::put('change-password{id}', [AdminController::class, 'ChangePassword'])->name('change-password');

    //User Controllers
    Route::get('show-user', [UserController::class, 'show'])->name('show-user');
    Route::get('add-user', [UserController::class, 'index'])->name('add-user');
    Route::post('store-user', [UserController::class, 'store'])->name('store-user');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('edit-user');
    Route::put('update-user{id}', [UserController::class, 'update'])->name('update-user');
    Route::post('changeStatus-user', [UserController::class, 'changeStatus'])->name('changeStatus-user');
    Route::post('delete-user', [UserController::class, 'delete'])->name('delete-user');
    Route::post('changePassword', [UserController::class, 'changePassword'])->name('changePassword');


    Route::get('show-customer', [CustomerController::class, 'show'])->name('show-customer');
    Route::get('user/{id}/view', [CustomerController::class, 'view'])->name('view-customer');


    Route::get('show-role', [RoleController::class, 'show'])->name('show-role');
    Route::get('add-role', [RoleController::class, 'index'])->name('add-role');
    Route::post('store-role', [RoleController::class, 'store'])->name('store-role');
    Route::get('role/{id}/edit', [RoleController::class, 'edit'])->name('edit-role');
    Route::put('update-role{id}', [RoleController::class, 'update'])->name('update-role');
    Route::post('destroy-role', [RoleController::class, 'destroy'])->name('destroy-role');


    Route::get('get-all-loan-applications', [LoanApplicationController::class, 'getAllData'])->name('get-all-loan-applications');
    Route::get('loan-application/{id}/view', [LoanApplicationController::class, 'getSingleData'])->name('view-loan-application');
    Route::get('loan-application/{id}/complete', [LoanApplicationController::class, 'completeApplication'])->name('complete-loan-application');
    Route::put('loan-applications/{id}/status', [LoanApplicationController::class, 'updateStatus'])->name('update-loan-application-status');
    Route::get('loan-applications/{id}/approved', [LoanApplicationController::class, 'approveLoan'])->name('approve-loan');


    Route::get('show-installment', [InstallmentController::class, 'index'])->name('show-installment');
    Route::get('show-installment/{id}/view', [InstallmentController::class, 'view'])->name('view-installment');
    Route::get('pay-installment', [InstallmentController::class, 'index'])->name('pay-installment');

    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    Route::prefix('recovery')->group(function () {
        Route::get('/create/{installmentDetailId}', [RecoveryController::class, 'create'])->name('recovery.create');
        Route::post('/store', [RecoveryController::class, 'store'])->name('recovery.store');
    });


    Route::get('show-product', [ProductController::class, 'show'])->name('show-product');
    Route::get('add-product', [ProductController::class, 'index'])->name('add-product');
    Route::post('store-product', [ProductController::class, 'store'])->name('store-product');
    Route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('edit-product');
    Route::put('update-product{id}', [ProductController::class, 'update'])->name('update-product');
    Route::post('destroy-product', [ProductController::class, 'destroy'])->name('destroy-product');


});



