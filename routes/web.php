<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AccountNameController;
use App\Http\Controllers\Admin\AccountTransactionController;
use App\Http\Controllers\Admin\AccountTypeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerNocController;
use App\Http\Controllers\Admin\Dashboard\SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\InstallmentController;
use App\Http\Controllers\Admin\JSBankController;
use App\Http\Controllers\Admin\NactaListController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RecoveryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorAccountController;
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
Route::get('/get-vendor-product-by-vendor', [AjaxController::class, 'getVendorProductByVendor']);
Route::get('/get-application-by-customer', [AjaxController::class, 'getApplicationByCustomer']);

Route::middleware(['auth'])->group(function () {

    Route::get('our-dashboard', [SuperAdminDashboardController::class, 'index'])->name('our-dashboard');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('update-password', [AdminController::class, 'updatePassword'])->name('update-password');
    Route::put('change-password{id}', [AdminController::class, 'ChangePassword'])->name('change-password');
    Route::get('failed-attempt-logs', [AdminController::class, 'failedLogs'])->name('failed-attempt-logs');
    Route::get('activity-logs', [AdminController::class, 'logActivityLists'])->name('activity-logs');

    //User Controllers
    Route::get('show-user', [UserController::class, 'show'])->name('show-user');
    Route::get('add-user', [UserController::class, 'index'])->name('add-user');
    Route::post('store-user', [UserController::class, 'store'])->name('store-user');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('edit-user');
    Route::put('update-user{id}', [UserController::class, 'update'])->name('update-user');
    Route::post('changeStatus-user', [UserController::class, 'changeStatus'])->name('changeStatus-user');
    Route::post('delete-user', [UserController::class, 'delete'])->name('delete-user');
    Route::post('changePassword', [UserController::class, 'changePassword'])->name('changePassword');


    Route::get('show-nacta', [NactaListController::class, 'index'])->name('show-nacta');
    Route::get('create-nacta', [NactaListController::class, 'create'])->name('create-nacta');
    Route::post('nacta/upload', [NactaListController::class, 'upload'])->name('upload-nacta');

    Route::get('show-customer', [CustomerController::class, 'show'])->name('show-customer');
    Route::get('show-customer-zindagi', [CustomerController::class, 'showZindagi'])->name('show-customer-zindagi');
    Route::get('add-customer', [CustomerController::class, 'index'])->name('add-customer');
    Route::post('store-customer', [CustomerController::class, 'store'])->name('store-customer');
    Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('edit-customer');
    Route::put('update-customer{id}', [CustomerController::class, 'update'])->name('update-customer');
    Route::get('user/{id}/view', [CustomerController::class, 'view'])->name('view-customer');
    Route::get('user/{id}/profile', [CustomerController::class, 'profile'])->name('view-customer-profile');
    Route::get('user/{id}/agreement', [CustomerController::class, 'agreement'])->name('view-customer-agreement');

//    noc routes
    Route::get('get-complete-loan-applications', [CustomerNocController::class, 'getAllData'])->name('get-complete-loan-applications');
    Route::get('loan-application/{id}/noc', [CustomerNocController::class, 'noc'])->name('view-loan-noc');


    Route::get('show-role', [RoleController::class, 'show'])->name('show-role');
    Route::get('add-role', [RoleController::class, 'index'])->name('add-role');
    Route::post('store-role', [RoleController::class, 'store'])->name('store-role');
    Route::get('role/{id}/edit', [RoleController::class, 'edit'])->name('edit-role');
    Route::put('update-role{id}', [RoleController::class, 'update'])->name('update-role');
    Route::post('destroy-role', [RoleController::class, 'destroy'])->name('destroy-role');


    Route::get('get-all-loan-applications', [LoanApplicationController::class, 'getAllData'])->name('get-all-loan-applications');
    Route::get('loan-application/{id}/edit', [LoanApplicationController::class, 'edit'])->name('edit-loan-application');
    Route::get('loan-application/{id}/view', [LoanApplicationController::class, 'getSingleData'])->name('view-loan-application');
    Route::get('loan-application/{id}/agreement', [LoanApplicationController::class, 'agreement'])->name('view-loan-agreement');
    Route::put('update-loan-application/{id}', [LoanApplicationController::class, 'updateApplication'])->name('update-loan-application');
    Route::post('loan-application/{id}/complete', [LoanApplicationController::class, 'completeApplication'])->name('complete-loan-application');
    Route::put('loan-applications/{id}/status', [LoanApplicationController::class, 'updateStatus'])->name('update-loan-application-status');
    Route::get('loan-applications/{id}/approved', [LoanApplicationController::class, 'approveLoan'])->name('approve-loan');
    Route::get('get-customer-loan-applications/{id}/{loanID}', [LoanApplicationController::class, 'getCustomerData'])->name('get-customer-loan-applications');
    Route::post('destroy-loan-application', [LoanApplicationController::class, 'destroy'])->name('destroy-loan-application');

    Route::get('create-loan-application', [LoanApplicationController::class, 'create'])->name('create-loan-application');
    Route::get('calculate-loan-application', [LoanApplicationController::class, 'calculateLoan'])->name('calculate-loan-application');
    Route::post('store-loan-application', [LoanApplicationController::class, 'storeApplication'])->name('store-loan-application');


    Route::get('show-installment', [InstallmentController::class, 'index'])->name('show-installment');
    Route::get('show-installment/{id}/view', [InstallmentController::class, 'view'])->name('view-installment');
    Route::get('pay-installment', [InstallmentController::class, 'index'])->name('pay-installment');
    Route::post('/installment/details/{id}/update-due-date', [InstallmentController::class, 'updateDueDate']);
    Route::post('/installment/details/{id}/update-issue-date', [InstallmentController::class, 'updateIssueDate']);
    Route::post('destroy-installment', [InstallmentController::class, 'destroy'])->name('destroy-installment');

    Route::get('/disbursement/{id}', [TransactionController::class, 'index'])->name('disbursement.show');
    Route::post('/store-disbursement', [TransactionController::class, 'storeDisbursement'])->name('disbursement.store');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/storeManual', [TransactionController::class, 'storeManual'])->name('transactions.storeManual');
    Route::post('/transactions/updateManual', [TransactionController::class, 'updateManual'])->name('transactions.updateManual');


    Route::prefix('jszindagi')->name('jszindagi.')->group(function () {
        Route::get('/index', [JSBankController::class, 'index'])->name('index');
        Route::get('/authorize', [JSBankController::class, 'getJSBankAuthorization'])->name('authorize');
        Route::get('/resetAuth', [JSBankController::class, 'resetAuth'])->name('resetAuth');
        Route::post('/generate/mpin', [JSBankController::class, 'generateMpin'])->name('generate.mpin');
        Route::get('/verifyAccount/{id}', [JSBankController::class, 'verifyAccount'])->name('verifyAccount');
        Route::post('/handle-wallet-transaction', [JSBankController::class, 'handleWalletTransaction'])->name('handleWalletTransaction');
        Route::get('/wallet-to-wallet/confirmation/{id}', [JSBankController::class, 'walletToWalletConfirmation'])->name('wallet-to-wallet.confirmation');
        Route::post('/wallet-to-wallet/confirm', [JSBankController::class, 'confirmWalletTransaction'])
            ->name('wallet-to-wallet.confirm');

    });
    Route::prefix('jsbank')->name('jsbank.')->group(function () {
        Route::post('/ibft-api', [JSBankController::class, 'JSBankIBFTAPI'])->name('ibftAPI');
        Route::post('/payment-ibft', [JSBankController::class, 'JSBankPaymentIBFT'])->name('paymentIBFT');
        Route::get('/get-token', [JSBankController::class, 'getTokenJSBank'])->name('getToken');
    });


    Route::prefix('recovery')->group(function () {
        Route::get('/create/{installmentDetailId}', [RecoveryController::class, 'create'])->name('recovery.create');
        Route::post('/store', [RecoveryController::class, 'store'])->name('recovery.store');
        Route::post('/installment/recover', [RecoveryController::class, 'storeRecovery']);
        Route::post('/installment/early', [RecoveryController::class, 'storeEarlySettlement']);
        Route::put('/recoveries/{id}', [RecoveryController::class, 'updateRecovery'])->name('recoveries.update');

    });


    Route::get('show-product', [ProductController::class, 'show'])->name('show-product');
    Route::get('add-product', [ProductController::class, 'index'])->name('add-product');
    Route::post('store-product', [ProductController::class, 'store'])->name('store-product');
    Route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('edit-product');
    Route::put('update-product{id}', [ProductController::class, 'update'])->name('update-product');
    Route::post('destroy-product', [ProductController::class, 'destroy'])->name('destroy-product');


    Route::get('show-disbursement-report', [ReportController::class, 'showDisbursementReport'])->name('show-disbursement-report');
    Route::get('get-disbursement-report', [ReportController::class, 'getDisbursementReport'])->name('get-disbursement-report');

    Route::get('show-overdue-report', [ReportController::class, 'showOverdueReport'])->name('show-overdue-report');
    Route::get('get-overdue-report', [ReportController::class, 'getOverdueReport'])->name('get-overdue-report');

    Route::get('show-collection-report', [ReportController::class, 'showCollectionReport'])->name('show-collection-report');
    Route::get('get-collection-report', [ReportController::class, 'getCollectionReport'])->name('get-collection-report');

    Route::get('show-profit-report', [ReportController::class, 'showProfitReport'])->name('show-profit-report');
    Route::get('get-profit-report', [ReportController::class, 'getProfitReport'])->name('get-profit-report');

    Route::get('show-outstanding-report', [ReportController::class, 'showOutstandingReport'])->name('show-outstanding-report');
    Route::get('get-outstanding-report', [ReportController::class, 'getOutstandingReport'])->name('get-outstanding-report');


    Route::get('show-aging-receivable-report', [ReportController::class, 'showAgingReceivableReport'])->name('show-aging-receivable-report');
    Route::get('get-aging-receivable-report', [ReportController::class, 'getAgingReceivableReport'])->name('get-aging-receivable-report');

    Route::get('show-provision-report', [ReportController::class, 'showProvisionReport'])->name('show-provision-report');
    Route::get('get-provision-report', [ReportController::class, 'getProvisionReport'])->name('get-provision-report');

    Route::get('show-financing-report', [ReportController::class, 'showFinanceReport'])->name('show-financing-report');
    Route::get('get-financing-report', [ReportController::class, 'getFinanceReport'])->name('get-financing-report');

    Route::get('show-penalty-report', [ReportController::class, 'showPenaltyReport'])->name('show-penalty-report');
    Route::get('get-penalty-report', [ReportController::class, 'getPenaltyReport'])->name('get-penalty-report');


    Route::get('show-principal-report', [ReportController::class, 'showPrincipalReport'])->name('show-principal-report');
    Route::get('get-principal-report', [ReportController::class, 'getPrincipalReport'])->name('get-principal-report');

    Route::get('show-interest-income-report', [ReportController::class, 'showInterestIncomeReport'])->name('show-interest-income-report');
    Route::get('get-interest-income-report', [ReportController::class, 'getInterestIncomeReport'])->name('get-interest-income-report');

    Route::get('show-early-settlement-report', [ReportController::class, 'showEarlySettlementReport'])->name('show-early-settlement-report');
    Route::get('get-early-settlement-report', [ReportController::class, 'getEarlySettlementReport'])->name('get-early-settlement-report');


    Route::get('show-invoice-report', [ReportController::class, 'showInvoiceReport'])->name('show-invoice-report');
    Route::get('get-invoice-report', [ReportController::class, 'getInvoiceReport'])->name('get-invoice-report');
    Route::get('/download-invoice-pdf', [ReportController::class, 'generatePDF'])->name('invoice.download');

    Route::get('show-complete-report', [ReportController::class, 'showCompleteReport'])->name('show-complete-report');
    Route::get('get-complete-report', [ReportController::class, 'getCompleteReport'])->name('get-complete-report');


    Route::get('show-expense-categories', [ExpenseCategoryController::class, 'index'])->name('show-expense-categories');
    Route::get('add-expense-category', [ExpenseCategoryController::class, 'create'])->name('add-expense-category');
    Route::post('store-expense-category', [ExpenseCategoryController::class, 'store'])->name('store-expense-category');
    Route::get('expense-category/{id}/edit', [ExpenseCategoryController::class, 'edit'])->name('edit-expense-category');
    Route::put('update-expense-category/{id}', [ExpenseCategoryController::class, 'update'])->name('update-expense-category');
    Route::post('destroy-expense-category', [ExpenseCategoryController::class, 'destroy'])->name('destroy-expense-category');

    Route::get('show-expense', [ExpenseController::class, 'index'])->name('show-expense');
    Route::get('add-expense', [ExpenseController::class, 'create'])->name('add-expense');
    Route::post('store-expense', [ExpenseController::class, 'store'])->name('store-expense');
    Route::get('expense/{id}/edit', [ExpenseController::class, 'edit'])->name('edit-expense');
    Route::put('update-expense/{id}', [ExpenseController::class, 'update'])->name('update-expense');
    Route::post('destroy-expense', [ExpenseController::class, 'destroy'])->name('destroy-expense');


    Route::get('show-account-type', [AccountTypeController::class, 'index'])->name('show-account-type');
    Route::get('add-account-type', [AccountTypeController::class, 'create'])->name('add-account-type');
    Route::post('store-account-type', [AccountTypeController::class, 'store'])->name('store-account-type');
    Route::get('account-type/{id}/edit', [AccountTypeController::class, 'edit'])->name('edit-account-type');
    Route::put('update-account-type/{id}', [AccountTypeController::class, 'update'])->name('update-account-type');
    Route::post('destroy-account-type', [AccountTypeController::class, 'destroy'])->name('destroy-account-type');


    Route::get('show-account-name', [AccountNameController::class, 'index'])->name('show-account-name');
    Route::get('add-account-name', [AccountNameController::class, 'create'])->name('add-account-name');
    Route::post('store-account-name', [AccountNameController::class, 'store'])->name('store-account-name');
    Route::get('account-name/{id}/edit', [AccountNameController::class, 'edit'])->name('edit-account-name');
    Route::put('update-account-name/{id}', [AccountNameController::class, 'update'])->name('update-account-name');
    Route::post('destroy-account-name', [AccountNameController::class, 'destroy'])->name('destroy-account-name');

    Route::get('show-account', [AccountController::class, 'index'])->name('show-account');
    Route::get('add-account', [AccountController::class, 'create'])->name('add-account');
    Route::post('store-account', [AccountController::class, 'store'])->name('store-account');
    Route::get('account/{id}/edit', [AccountController::class, 'edit'])->name('edit-account');
    Route::put('update-account/{id}', [AccountController::class, 'update'])->name('update-account');
    Route::post('destroy-account', [AccountController::class, 'destroy'])->name('destroy-account');

    Route::get('show-vendor-account', [VendorAccountController::class, 'index'])->name('show-vendor-account');
    Route::get('add-vendor-account', [VendorAccountController::class, 'create'])->name('add-vendor-account');
    Route::post('store-vendor-account', [VendorAccountController::class, 'store'])->name('store-vendor-account');
    Route::get('vendor-account/{id}/edit', [VendorAccountController::class, 'edit'])->name('edit-vendor-account');
    Route::put('update-vendor-account/{id}', [VendorAccountController::class, 'update'])->name('update-vendor-account');
    Route::post('destroy-vendor-account', [VendorAccountController::class, 'destroy'])->name('destroy-vendor-account');

    Route::get('show-account-transaction', [AccountTransactionController::class, 'index'])->name('show-account-transaction');
    Route::get('show-account-transaction-history/{id}', [AccountTransactionController::class, 'getHistoryByAccountID'])->name('show-account-transaction-history');
    Route::get('add-account-transaction', [AccountTransactionController::class, 'create'])->name('add-account-transaction');
    Route::post('store-account-transaction', [AccountTransactionController::class, 'store'])->name('store-account-transaction');
    Route::get('account-transaction/{id}/edit', [AccountTransactionController::class, 'edit'])->name('edit-account-transaction');
    Route::put('update-account-transaction/{id}', [AccountTransactionController::class, 'update'])->name('update-account-transaction');
    Route::post('destroy-account-transaction', [AccountTransactionController::class, 'destroy'])->name('destroy-account-transaction');


});



