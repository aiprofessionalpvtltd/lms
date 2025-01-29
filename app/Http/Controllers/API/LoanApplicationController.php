<?php

namespace App\Http\Controllers\API;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstallmentDetailResource;
use App\Http\Resources\LoanApplicationResource;
use App\Http\Resources\LoanApplicationTrackingResource;
use App\Http\Resources\LoanAttachmentResource;
use App\Http\Resources\UserResource;
use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\LoanApplicationHistory;
use App\Models\LoanApplicationProduct;
use App\Models\LoanAttachment;
use App\Models\LoanDuration;
use App\Models\LoanPurpose;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends BaseController
{


//    public function calculateLoan(Request $request)
//    {
//        $loanAmount = $request->input('loan_amount');
//        $months = $request->input('months');
//        $processingFee  = $request->input('processing_fee') ?? '0.027';
//        $interestRate = $request->input('interest_rate') ?? '0.3';
//
//        // Validate input
//        if (!in_array($months, [3, 6, 9, 12])) {
//            return response()->json(['error' => 'Invalid month duration. Choose from 3, 6, 9, or 12 months.'], 400);
//        }
//
//        // Constants
//        $processingFeeRate = $processingFee;
//        $riskPremiumRate = 0.02;
//        $operatingCostsRate = $interestRate;
//        $profitMarginRate = 0.20;
//        $costOfFundsRate = $this->getCostOfFundsRate($months);
//
//        // Calculate processing fee
//        $processingFee = $loanAmount * $processingFeeRate;
//
//        // Calculate total markup
//        $totalMarkup = ($costOfFundsRate + $riskPremiumRate + $operatingCostsRate + $profitMarginRate) * ($months / 12) * $loanAmount;
//
//        // Calculate total payable amount
//        $totalPayableAmount = $loanAmount + $processingFee + $totalMarkup;
//
//        // calculate overall markup
//        $overallMarkup = $processingFee + $totalMarkup;
//
//        // calculate Monthly Installment to Pay
//        $monthlyInstallment = $totalPayableAmount / $months;
//
//        return $this->sendResponse(
//            [
//                'loan_amount' => $loanAmount,
//                'months' => $months,
//                'processing_fee' => round($processingFee),
//                'total_markup' => round($totalMarkup),
//                'over_markup' => round($overallMarkup),
//                'monthly_installment' => round($monthlyInstallment),
//                'total_payable_amount' => round($totalPayableAmount),
//            ],
//            'Loan calculated successfully.'
//        );
//
//
//    }
//
//    private function getCostOfFundsRate($months)
//    {
//        switch ($months) {
//            case 6:
//                return 0.05; // 5% for 6 months
//            case 9:
//                return 0.075; // 7.5% for 9 months
//            case 12:
//                return 0.10; // 10% for 12 months
//            case 3:
//                return 0.05; // 5% for 3 months
//            default:
//                return 0; // No cost of funds for 3 months
//        }
//    }

    public function create()
    {
        $title = 'Create Loan Application';
        $customers = User::with(['roles', 'profile', 'tracking'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Customer');
            })->get();
        $products = Product::all();
        $loanDurations = LoanDuration::orderBy('id', 'asc')->get();
        $loanPurposes = LoanPurpose::all();
        return view('admin.loan_applications.create',
            compact('title', 'customers', 'products', 'loanDurations', 'loanPurposes'));
    }


    public function storeApplication(Request $request)
    {
//        dd($request->all());
        // Fetch maximum loan amount from .env
        $maxAmount = env('MAX_AMOUNT', 300000); // Default to 300,000 PKR

        // Validate the request
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'loan_amount' => 'required|numeric',
            'loan_duration_id' => 'required',
            'loan_purpose_id' => 'required|exists:loan_purposes,id',
            'down_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'bank_document' => 'required|file|mimes:pdf,jpg,png|max:2048', // Example validation for documents
            'salary_slip_document' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'signature' => 'nullable|string', // Ensure signature is a base64 string
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Fetch user and validate specific conditions
            $user = User::findOrFail($request->customer_id);

            if ($request->loan_amount > $maxAmount) {
                return redirect()->route('get-all-loan-applications')->with('error', "The loan amount cannot exceed " . number_format($maxAmount) . " PKR.");
            }

//            $existingApplication = LoanApplication::where('user_id', $user->id)->where('is_completed', 0)->first();

//            if ($existingApplication) {
//                return redirect()->back()->with('error', 'An application is already in progress. A new application cannot be submitted.');
//            }

            // Prepare loan application data
            $loanDuration = LoanDuration::where('value', $request->loan_duration_id)->firstOrFail();
            $loanApplicationData = $this->prepareLoanApplicationData($user, $loanDuration, $request);
            $loanApplication = LoanApplication::create($loanApplicationData);

            $request->merge(['months' => $loanDuration->value]);

            // Create loan application history
            $this->createLoanHistory($loanApplication, $user);

            // Calculate loan details
            $loanCalculated = $this->calculateLoan($request);

            if (isset($loanCalculated->getData()->error)) {

                return $loanCalculated->getData()->error;

            }

            $calculatedData = $loanCalculated->getData()->data;


            $request->merge(['loan_duration_id' => $loanDuration->id]);

            // Save calculated loan details
            $this->saveLoanApplicationProduct($loanApplication, $request, $calculatedData);

            // Handle document uploads
            $this->handleAttachments($loanApplication, $user, $request);

            // Update tracking data for user
            $user->tracking->update(['is_bank_statement' => 1]);

            LogActivity::addToLog('Loan Application  ' . $loanApplication->application_id . ' Created');

            DB::commit();

            return redirect()->route('get-all-loan-applications')->with('success', 'Loan Application Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Get the status from the request, defaulting to 'pending' if not provided
        $loanApplicationID = $id;
        $customers = User::with(['roles', 'profile', 'tracking'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Customer');
            })->get();
        $products = Product::all();
        $loanDurations = LoanDuration::orderBy('id', 'asc')->get();
        $loanPurposes = LoanPurpose::all();
        try {
            // Fetch loan applications based on the status
            $loanApplication = LoanApplication::with('calculatedProduct', 'attachments')->find($loanApplicationID);

            // Check if any loan applications are found
            if ($loanApplication == null) {
                return $this->sendError('No Loan Applications found');
            }


//            dd($loanApplication);
            LogActivity::addToLog('Loan Application ' . $loanApplication->application_id . ' Edit');

            return view('admin.loan_applications.edit', compact('loanApplication', 'customers', 'products', 'loanDurations', 'loanPurposes'));


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }

    public function updateApplication(Request $request, $loanApplicationId)
    {
        // Fetch maximum loan amount from .env
        $maxAmount = env('MAX_AMOUNT', 300000); // Default to 300,000 PKR

        // Validate the request
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'loan_amount' => 'required|numeric',
            'loan_duration_id' => 'required',
            'loan_purpose_id' => 'required|exists:loan_purposes,id',
            'down_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'bank_document' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // Document is optional for update
            'salary_slip_document' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // Document is optional for update
            'signature' => 'nullable|string', // Ensure signature is a base64 string
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Fetch loan application to update
            $loanApplication = LoanApplication::findOrFail($loanApplicationId);

            // Fetch user and validate specific conditions
            $user = User::findOrFail($request->customer_id);

            if ($request->loan_amount > $maxAmount) {
                return redirect()->back()->with('error', "The loan amount cannot exceed " . number_format($maxAmount) . " PKR.");
            }

            // Update loan application data
            $loanDuration = LoanDuration::where('value', $request->loan_duration_id)->firstOrFail();
            $loanApplication->update([
                'product_id' => $request->product_id,
                'loan_amount' => $request->loan_amount,
                'loan_duration_id' => $loanDuration->id,
                'loan_purpose_id' => $request->loan_purpose_id,
                'approved_by' => auth()->id(),
            ]);

            $request->merge(['months' => $loanDuration->value]);

            // Calculate loan details
            $loanCalculated = $this->calculateLoan($request);

            if (isset($loanCalculated->getData()->error)) {
                return $loanCalculated->getData()->error;
            }

            $calculatedData = $loanCalculated->getData()->data;

            // Save updated loan details
            $loanApplicationProduct = $loanApplication->calculatedProduct()->firstOrNew(['loan_application_id' => $loanApplication->id]);

            $loanApplicationProduct->fill([
                'request_for' => $request->input('request_for'),
                'loan_duration_id' => $loanDuration->id,
                'loan_amount' => $calculatedData->loan_amount,
                'down_payment_percentage' => $calculatedData->down_payment_percentage,
                'processing_fee_percentage' => $calculatedData->processing_fee_percentage,
                'interest_rate_percentage' => $calculatedData->interest_rate_percentage,
                'financed_amount' => $calculatedData->financed_amount,
                'processing_fee_amount' => round($calculatedData->processing_fee_amount, 2),
                'down_payment_amount' => round($calculatedData->down_payment_amount, 2),
                'total_upfront_payment' => round($calculatedData->total_upfront_payment, 2),
                'disbursement_amount' => round($calculatedData->disbursement_amount, 2),
                'total_interest_amount' => round($calculatedData->total_interest_amount, 2),
                'total_repayable_amount' => round($calculatedData->total_repayable_amount, 2),
                'monthly_installment_amount' => round($calculatedData->monthly_installment_amount, 2),
            ]);
            $loanApplicationProduct->save();

            // Handle document uploads (only if a new document is provided)
            if ($request->hasFile('bank_document')) {
                $bankDocumentPath = $request->file('bank_document')->store('documents', 'public');
                $this->saveAttachment($loanApplication, $user, 1, $bankDocumentPath);
            }

            if ($request->hasFile('salary_slip_document')) {
                $salarySlipPath = $request->file('salary_slip_document')->store('documents', 'public');
                $this->saveAttachment($loanApplication, $user, 2, $salarySlipPath);
            }

            if ($request->signature) {
                $signaturePath = $this->saveBase64Image($request->signature, 'documents');
                $this->saveAttachment($loanApplication, $user, 3, $signaturePath);
            }

            // Update tracking data for user (if required)
            $user->tracking->update(['is_bank_statement' => 1]);

            LogActivity::addToLog('Loan Application ' . $loanApplication->application_id . ' Updated');

            DB::commit();

            return redirect()->route('get-all-loan-applications')->with('success', 'Loan Application Updated Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * Prepare loan application data.
     */
    private function prepareLoanApplicationData($user, $loanDuration, $request)
    {
        return [
            'application_id' => $this->generateLoanApplicationId($user),
            'name' => $user->name,
            'email' => $user->email,
            'product_id' => $request->product_id,
            'loan_amount' => $request->loan_amount,
            'loan_duration_id' => $loanDuration->id,
            'loan_purpose_id' => $request->loan_purpose_id,
            'user_id' => $user->id,
            'status' => 'pending',
            'approved_by' => auth()->id(),
            'is_submitted' => true,
        ];
    }

    /**
     * Create loan application history.
     */
    private function createLoanHistory($loanApplication, $user)
    {
        LoanApplicationHistory::create([
            'loan_application_id' => $loanApplication->id,
            'from_user_id' => $user->id,
            'from_role_id' => 3, // Customer role ID
            'to_user_id' => auth()->user()->id,
            'to_role_id' => auth()->user()->roles->first()->id,
            'status' => 'pending',
            'remarks' => 'Application Submitted By Admin',
        ]);
    }

    /**
     * Save loan application product details.
     */
    private function saveLoanApplicationProduct($loanApplication, $request, $calculatedData)
    {

        LoanApplicationProduct::create([
            'request_for' => $request->input('request_for'),
            'loan_application_id' => $loanApplication->id,
            'product_id' => $request->input('product_id'),
            'loan_duration_id' => $request->input('loan_duration_id'),
            'loan_amount' => $calculatedData->loan_amount,
            'down_payment_percentage' => $calculatedData->down_payment_percentage,
            'processing_fee_percentage' => $calculatedData->processing_fee_percentage,
            'interest_rate_percentage' => $calculatedData->interest_rate_percentage,
            'financed_amount' => $calculatedData->financed_amount,
            'processing_fee_amount' => round($calculatedData->processing_fee_amount, 2),
            'down_payment_amount' => round($calculatedData->down_payment_amount, 2),
            'total_upfront_payment' => round($calculatedData->total_upfront_payment, 2),
            'disbursement_amount' => round($calculatedData->disbursement_amount, 2),
            'total_interest_amount' => round($calculatedData->total_interest_amount, 2),
            'total_repayable_amount' => round($calculatedData->total_repayable_amount, 2),
            'monthly_installment_amount' => round($calculatedData->monthly_installment_amount, 2),
        ]);
    }

    /**
     * Handle document uploads.
     */
    private function handleAttachments($loanApplication, $user, $request)
    {
        // Upload bank document
        $bankDocumentPath = $request->file('bank_document')->store('documents', 'public');
        $this->saveAttachment($loanApplication, $user, 1, $bankDocumentPath);

        // Upload salary slip document
        $salarySlipPath = $request->file('salary_slip_document')->store('documents', 'public');
        $this->saveAttachment($loanApplication, $user, 2, $salarySlipPath);

        // Upload signature as base64
        if ($request->signature) {
            $signaturePath = $this->saveBase64Image($request->signature, 'documents');
            $this->saveAttachment($loanApplication, $user, 3, $signaturePath);
        }

    }

    /**
     * Save individual attachment.
     */
    private function saveAttachment($loanApplication, $user, $typeId, $path)
    {
        LoanAttachment::updateOrCreate(
            [
                'loan_application_id' => $loanApplication->id,
                'user_id' => $user->id,
                'document_type_id' => $typeId,
            ],
            [
                'path' => $path,
            ]
        );
    }

//    ====================================================API WORK STARTED======================================================
    public function calculateLoan(Request $request)
    {
        $loanAmount = $request->input('loan_amount');
        $months = $request->input('months');
        $requestType = $request->input('request_for');
        $old_interest_rate = $request->input('old_interest_rate');
        $old_processing_fee_amount = $request->input('old_processing_fee_amount');
        $downPaymentPercentage = $request->input('down_payment_percentage', 10); // Default to 10% if not specified

        // Validate input
//        if (!in_array($months, [3, 6, 9, 12])) {
//            return response()->json(['error' => 'Invalid month duration. Choose from 3, 6, 9, or 12 months.'], 400);
//        }

        if ($requestType === 'product') {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['error' => 'Product not found.'], 404);
            }

            if ($old_processing_fee_amount) {
                $productProcessingFeePercentage = $old_processing_fee_amount;
            } else {
                $productProcessingFeePercentage = $product->processing_fee;

            }

            if ($old_interest_rate) {
                $productInterestRate = $old_interest_rate;
            } else {
                $productInterestRate = $product->interest_rate;
            }

            $processingFeeRate = $productProcessingFeePercentage / 100;
            $interestRate = ($productInterestRate / 100) / 12;

            $downPayment = $loanAmount * ($downPaymentPercentage / 100);
            $processingFeeAmount = $loanAmount * $processingFeeRate;
            $totalUpfrontPayment = $downPayment + $processingFeeAmount;
            $financedAmount = $loanAmount - round($downPayment);
            $disbursementAmount = $financedAmount;

            $totalInterestAmount = $financedAmount * $interestRate * $months;
            $totalRepayableAmount = $financedAmount + $totalInterestAmount;
            $monthlyInstallmentAmount = $totalRepayableAmount / $months;

        } elseif ($requestType === 'loan') {
            $standardProcessingFeePercentage = env('STANDARD_PROCESSING_FEE');
            $standardInterestRate = env('STANDARD_INTEREST');
            $downPaymentPercentage = 0;
            $totalUpfrontPayment = 0;
            $productId = NULL;

            if ($old_processing_fee_amount) {
                $standardProcessingFeePercentage = $old_processing_fee_amount;
            }

            if ($old_interest_rate) {
                $standardInterestRate = $old_interest_rate;
            }

            $processingFeeRate = $standardProcessingFeePercentage / 100;
            $interestRate = ($standardInterestRate / 100) / 12;

            $downPayment = $loanAmount * ($downPaymentPercentage / 100);
            $financedAmount = $loanAmount - round($downPayment);
            $processingFeeAmount = $financedAmount * $processingFeeRate;
            $disbursementAmount = $financedAmount;

            $totalInterestAmount = $financedAmount * $interestRate * $months;

            $totalRepayableAmount = $financedAmount + $totalInterestAmount + $processingFeeAmount;
            $monthlyInstallmentAmount = $totalRepayableAmount / $months;
        } else {
            return $this->sendError('error', 'Invalid request_for value.');
        }

        return $this->sendResponse(
            [
                'request_for' => $requestType,
                'product_id' => $productId,
                'loan_amount' => $loanAmount,
                'months' => $months,
                'down_payment_percentage' => $downPaymentPercentage,
                'processing_fee_percentage' => round($productProcessingFeePercentage ?? $standardProcessingFeePercentage, 2),
                'interest_rate_percentage' => round($productInterestRate ?? $standardInterestRate, 2),
                'financed_amount' => $financedAmount,
                'processing_fee_amount' => round($processingFeeAmount),
                'down_payment_amount' => round($downPayment),
                'total_upfront_payment' => round($totalUpfrontPayment),
                'disbursement_amount' => round($disbursementAmount),
                'total_interest_amount' => round($totalInterestAmount),
                'total_repayable_amount' => round($totalRepayableAmount),
                'monthly_installment_amount' => round($monthlyInstallmentAmount),
            ],
            'Loan calculated successfully.'
        );
    }


    public function getAllData(Request $request)
    {
        // Get the status from the request, defaulting to 'pending' if not provided
        $status = $request->get('status');

        try {
            $loanApplications = [];

            $authUser = auth()->user();

            $loanApplications = LoanApplication::query()
                ->when($status, function ($query, $status) {
                    // Apply the status filter only if $status is provided
                    return $query->where('status', $status);
                })
                ->when(!$authUser->hasRole(['Management', 'Super Admin']), function ($query) use ($authUser) {
                    // If the user is not management or admin, check the latest history's to_user_id
                    return $query->whereHas('getLatestHistory', function ($historyQuery) use ($authUser) {
                        $historyQuery->where('to_user_id', $authUser->id);
                    });
                })
                ->get();


            // Check if any loan applications are found
            if ($loanApplications->isEmpty()) {
                if ($request->expectsJson()) {
                    return $this->sendError('No Loan Applications found for the given status.');

                } else {


                    return view('admin.loan_applications.index', compact('loanApplications'));
                }
            }

            if ($request->expectsJson()) {
                // Return the loan applications as a response
                return $this->sendResponse(
                    LoanApplicationResource::collection($loanApplications),
                    'Loan Applications retrieved successfully.'
                );
            } else {
                LogActivity::addToLog('Loan Applications Listing Viewed');

                return view('admin.loan_applications.index', compact('loanApplications'));
            }

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }

    public function getCustomerData($id, $loanID)
    {
        try {
            $loanApplications = [];

            $loanApplications = LoanApplication::with('getLatestHistory')
                ->where('id', '!=', $loanID) // Exclude the current loan application
                ->where('user_id', $id)->get();


            return view('admin.loan_applications.index', compact('loanApplications'));


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }

    public function agreement($id)
    {
        $title = 'Agreement User';
        $loanApplicationID = $id;

        try {
            // Fetch loan applications based on the status
            $loanApplication = LoanApplication::find($loanApplicationID);

            // Check if any loan applications are found
            if ($loanApplication == null) {
                return $this->sendError('No Loan Applications found');
            }

            // Retrieve the user's existing or previous loan applications
            $userId = $loanApplication->user_id;


            $customer = User::with('roles', 'profile', 'bank_account', 'tracking',
                'employment.employmentStatus', 'employment.incomeSource', 'employment.existingLoan',
                'familyDependent', 'education.education', 'references.relationship')
                ->find($userId);


            $loanApplication->load('loanDuration');

            $loanApplicationProduct = $loanApplication->calculatedProduct;
            $loanApplicationDisbursment= $loanApplication->transaction;
//            dd($loanApplicationDisbursment);
            $loanApplicationFirstInstallments = $loanApplication->getLatestInstallment->details[0];


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }

        return view('admin.customer.agreement', compact('title', 'customer', 'loanApplication',
            'loanApplicationProduct', 'loanApplicationFirstInstallments' ,'loanApplicationDisbursment'));
    }


    public function getSingleData(Request $request, $id)
    {
        // Get the status from the request, defaulting to 'pending' if not provided
        $loanApplicationID = $id;

        try {
            // Fetch loan applications based on the status
            $loanApplication = LoanApplication::find($loanApplicationID);

            // Check if any loan applications are found
            if ($loanApplication == null) {
                return $this->sendError('No Loan Applications found');
            }

            // Retrieve the user's existing or previous loan applications
            $userId = $loanApplication->user_id;
            $previousLoans = LoanApplication::where('user_id', $userId)
                ->where('id', '!=', $loanApplicationID) // Exclude the current loan application
                ->get();


            if ($request->expectsJson()) {
                // Return the loan applications as a response
                return $this->sendResponse(
                    new LoanApplicationResource($loanApplication),
                    'Loan Applications retrieved successfully.'
                );
            } else {
                $appUser = $loanApplication->user;
                $provinceID = $appUser->province_id;
                $districtID = $appUser->district_id;
                $cityID = $appUser->city_id;

                $roleId = 4; // for loan onboarding

                $toUsers = User::where('id', '!=', auth()->user()->id)
                    ->whereHas('roles', function ($query) use ($roleId, $provinceID, $districtID, $cityID) {
                        $query->where('id', '>=', $roleId)->where('province_id', $provinceID)
                            ->where('district_id', $districtID)
                            ->where('city_id', $cityID);;
                    })->with('roles:name,id')->get();

                $loanApplication->load('loanDuration');

                $productProcessingFee = 0;
                $productInterestFee = 0;
                // get product detail
                $product = $loanApplication->product;
                if ($product) {
                    $productProcessingFee = $product->processing_fee / 100;
                    $productInterestFee = $product->interest_rate / 100;
                }


                $extraParameterForLoan = [
                    'loan_amount' => $loanApplication->loan_amount,
                    'months' => $loanApplication->loanDuration->value,
                    'processing_fee' => $productProcessingFee,
                    'interest_rate' => $productInterestFee,

                ];

                $request->merge($extraParameterForLoan);

                $loanApplication->load('calculatedProduct');
                $loanApplicationProduct = $loanApplication->calculatedProduct;

                LogActivity::addToLog('Loan Application ' . $loanApplication->application_id . ' Viewed');

                return view('admin.loan_applications.view', compact('loanApplication', 'toUsers', 'loanApplicationProduct', 'previousLoans'));
            }


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }


    public function getUserData(Request $request)
    {
        // Get the status from the request, defaulting to 'pending' if not provided
        $status = $request->get('status', 'pending');
        $userID = auth()->user()->id;

        try {
            // Fetch loan applications based on the status
//            if ($status) {
//                // Fetch loan applications based on the status
//                $loanApplications = LoanApplication::where('status', $status)->where('user_id', $userID)->get();
//            } else {
            $loanApplications = LoanApplication::where('user_id', $userID)->get();

//            }

            // Check if any loan applications are found
            if ($loanApplications->isEmpty()) {
                return $this->sendError('No Loan Applications found for the given status.');
            }

            // Return the loan applications as a response
            return $this->sendResponse(
                ['loan_application' => LoanApplicationResource::collection($loanApplications)],
                'Loan Applications retrieved successfully.'
            );

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }

    public function getApplicationTracking(Request $request)
    {

        $userID = auth()->id();  // A cleaner way to get the authenticated user ID

        try {
            // Fetch the first loan application that matches the status and user ID, and is not completed
            $loanApplication = LoanApplication::with('calculatedProduct')->where([
                ['is_completed', '=', false],
                ['user_id', '=', $userID]
            ])->latest()->first();

            // Check if a loan application is found
            if (!$loanApplication) {
                return $this->sendResponse(
                    ['loan_application' => [
                        "is_completed" => false,
                        "is_application_submitted" => false,
                        "is_documents_uploaded" => false,
                        "is_process_completed" => false,
                    ]],
                    'No Loan application available.'
                );
            }

            // Return the loan application as a response
            return $this->sendResponse(
                ['loan_application' => new LoanApplicationTrackingResource($loanApplication)],
                'Loan application retrieved successfully.'
            );

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred while retrieving the loan application.' . $e->getMessage());
        }
    }


    public function store(Request $request)
    {
        // Get the max amount from .env file
        $maxAmount = env('MAX_AMOUNT', 300000); // Fallback to 300000 if not set in .env

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
//            'loan_amount' => 'sometimes|numeric',
//            'product_id' => 'sometimes|exists:products,id',
//            'loan_duration_id' => 'sometimes|exists:loan_durations,id',
//            'product_service_id' => 'required|exists:product_services,id',
//            'loan_purpose_id' => 'required|exists:loan_purposes,id',
//            'address' => 'required|string',
//            'reference_contact_1' => 'required|string|max:255',
//            'reference_contact_2' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        DB::beginTransaction();

        try {

            $authUser = auth::user();
            if ($request->loan_amount > $maxAmount) {
                return $this->sendError('Loan Amount Limit', 'The loan amount cannot exceed ' . number_format($maxAmount) . ' PKR.');

            }
            $userID = $authUser->id;
            $userRoleID = $authUser->roles->first()->id;

            $runningLoanApplication = LoanApplication::where('user_id', $userID)->where('is_completed', 0)->first();

            if ($runningLoanApplication) {
                // Check if a running application exists
                return $this->sendError('An application is already in progress. A new application cannot be submitted.', new LoanApplicationResource($runningLoanApplication));
            }


            $provinceID = $authUser->province_id;
            $districtID = $authUser->district_id;
            $cityID = $authUser->city_id;

            $roleId = 4; // for Loan Onboarding

            $toUsers = User::whereHas('roles', function ($query) use ($roleId, $provinceID, $districtID, $cityID) {
                $query->where('id', $roleId)
                    ->where('province_id', $provinceID)
                    ->where('district_id', $districtID)
                    ->where('city_id', $cityID);
            })->first();

            if (!$toUsers) {

                $toUsers = User::whereHas('roles', function ($query) use ($roleId, $provinceID, $districtID, $cityID) {
                    $query->where('id', 2);
                })->first();

            }

            $toRoleID = $toUsers->roles->first()->id;


            $loanApplication = LoanApplication::create([
                'application_id' => $this->generateLoanApplicationId($authUser),
                'name' => $request->name,
                'email' => $request->email,
                'user_id' => $userID,
                'status' => 'pending',
            ]);

            LoanApplicationHistory::create([
                'loan_application_id' => $loanApplication->id,
                'from_user_id' => $userID,
                'from_role_id' => $userRoleID,
                'to_user_id' => $toUsers->id,
                'to_role_id' => $toRoleID,
                'status' => 'pending',
                'remarks' => 'Application Submitted By Customer',
            ]);


            $loanApplication->load('attachments');
            DB::commit();
            return $this->sendResponse([
                'loan_application' => new LoanApplicationResource($loanApplication)
            ], 'Loan Application Submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function storeDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_document' => 'required|file|mimes:pdf,jpg,png,doc,docx',
            'salary_slip_document' => 'required|file|mimes:pdf,jpg,png,doc,docx',
            'signature' => 'required|string',
            'loan_purpose_id' => 'required|exists:loan_purposes,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $loanApplication = LoanApplication::find($request->id);

            if (!$loanApplication) {
                return $this->sendError('Loan Application not found.');
            }
            $authUser = auth::user();

            $loanApplication->loan_purpose_id = $request->loan_purpose_id;
            $loanApplication->save();

            // Handle bank document upload
            $bankDocumentPath = $request->bank_document->store('documents', 'public');
            LoanAttachment::updateOrCreate(
                [
                    'loan_application_id' => $loanApplication->id,
                    'user_id' => $authUser->id,
                    'document_type_id' => 1,  // Type ID for Bank Document
                ],
                [
                    'path' => $bankDocumentPath,
                ]
            );

            // Handle salary slip document upload
            $salarySlipDocumentPath = $request->salary_slip_document->store('documents', 'public');
            LoanAttachment::updateOrCreate(
                [
                    'loan_application_id' => $loanApplication->id,

                    'user_id' => $authUser->id,
                    'document_type_id' => 2,  // Type ID for Salary Slip Document
                ],
                [
                    'path' => $salarySlipDocumentPath,
                ]
            );

            // Handle signature as base64 image upload
            $signaturePath = $this->saveBase64Image($request->signature, 'documents');
            LoanAttachment::updateOrCreate(
                [
                    'loan_application_id' => $loanApplication->id,

                    'user_id' => $authUser->id,
                    'document_type_id' => 3,  // Type ID for Signature
                ],
                [
                    'path' => $signaturePath,
                ]
            );

            $authUser->load('tracking', 'familyDependent', 'bank_account', 'profile', 'education', 'employment', 'references');

            $authUser->tracking->update(['is_bank_statement' => 1]);


            DB::commit();
            return $this->sendResponse([
                'loan_application' => new LoanApplicationResource($loanApplication),
                'user' => new UserResource($authUser),
            ], 'Your loan application has been submitted successfully and is under review.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError([], 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function completeApplication(Request $request, $id)
    {


        $loanApplication = LoanApplication::find($id);

        if (!$loanApplication) {
            return redirect()->back()->with('error', 'Loan Application not found.');
        }


        $loanApplication->is_completed = $request->is_completed;
        $loanApplication->save();

        return redirect()->route('get-all-loan-applications')->with('success', 'Loan Application Completed successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,rejected',
            'remarks' => 'required',
            'to_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $loanApplication = LoanApplication::find($id);

        if (!$loanApplication) {
            return redirect()->back()->with('error', 'Loan Application not found.');
        }

        $loanApplication->status = $request->status;
        $loanApplication->save();


        $userID = auth::user()->id;
        $userRoleID = auth()->user()->roles->first()->id;

        $toUsers = User::find($request->to_user_id);
        $toRoleID = $toUsers->roles->first()->id;


        LoanApplicationHistory::create([
            'loan_application_id' => $loanApplication->id,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'from_user_id' => $userID,
            'from_role_id' => $userRoleID,
            'to_user_id' => $toUsers->id,
            'to_role_id' => $toRoleID,
        ]);

        return redirect()->route('get-all-loan-applications')->with('success', 'Loan Application status updated successfully.');
    }


    public function checkEligibility(Request $request)
    {
        // Assuming you have a User model with related information such as CNIC, age, employment status, income, bank statement, and address proof

        // Fetch the authenticated user
        $user = Auth::user();

        // Initialize an array to store eligibility checks
        $eligibilityChecks = [];

        // 1. Age Check
        $age = Carbon::parse($user->profile->dob)->age;

        if ($age >= 21 && $age <= 60) {
            $eligibilityChecks['age'] = true;
        } else {
            $eligibilityChecks['age'] = false;
        }

        // 2. CNIC Check
        if (!empty($user->profile->cnic_no)) {
            $eligibilityChecks['cnic'] = true;
        } else {
            $eligibilityChecks['cnic'] = false;
        }

        // 3. Employment Status Check

        if ($user->employment->employment_status_id == 1 && $user->employment->employment_duration >= 6) {
            $eligibilityChecks['employment'] = true;

        } elseif ($user->employment->employment_status_id == 2 && $user->employment->employment_duration >= 12) {
            $eligibilityChecks['employment'] = true;

        } else {
            $eligibilityChecks['employment'] = false;
        }

        // 4. Minimum Monthly Income Check
        if ($user->employment->net_income >= 30000) {
            $eligibilityChecks['income'] = true;
        } else {
            $eligibilityChecks['income'] = false;
        }

        // 5. Bank Statement Check (last 6 months)
        if (!empty($user->tracking->is_bank_statement)) {
            $eligibilityChecks['bank_statement'] = true;
        } else {
            $eligibilityChecks['bank_statement'] = false;
        }

        // 6. Address Proof Check
        if (!empty($user->tracking->is_address_proof)) {
            $eligibilityChecks['address_proof'] = true;
        } else {
            $eligibilityChecks['address_proof'] = false;
        }

        // Check if all criteria are met
        if (collect($eligibilityChecks)->contains(false)) {
            return $this->sendError('Eligibility Criteria Not Met', $eligibilityChecks);
        }

        return $this->sendResponse('Eligible for Sarmaya Loan', 'You meet all the eligibility criteria.');
    }

    public function storeCalculation(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'request_for' => 'required|string|in:product,loan',
            'loan_application_id' => 'required|exists:loan_applications,id',
            'product_id' => 'nullable|exists:products,id',
            'loan_duration_id' => 'required|exists:loan_durations,id',
            'loan_amount' => 'required|numeric|min:0',
            'down_payment_percentage' => 'required|numeric|min:0|max:100',
            'processing_fee_percentage' => 'required|numeric|min:0|max:100',
            'interest_rate_percentage' => 'required|numeric|min:0|max:100',
            'months' => 'required|integer|in:3,6,9,12',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {

            $loanApplication = LoanApplication::find($request->input('loan_application_id'));

            if (!$loanApplication) {
                return redirect()->back()->with('error', 'Loan Application not found.');
            }


            // Calculate loan details
            $loanAmount = $request->input('loan_amount');
            $months = $request->input('months');
            $downPaymentPercentage = $request->input('down_payment_percentage');
            $processingFeePercentage = $request->input('processing_fee_percentage');
            $interestRatePercentage = $request->input('interest_rate_percentage');

            $downPaymentAmount = $loanAmount * ($downPaymentPercentage / 100);
            $financedAmount = $loanAmount - round($downPaymentAmount);
            $processingFeeAmount = $financedAmount * ($processingFeePercentage / 100);
            $disbursementAmount = $financedAmount - $processingFeeAmount;

            $totalInterestAmount = $financedAmount * ($interestRatePercentage / 100);
            $totalRepayableAmount = $financedAmount + $totalInterestAmount;
            $monthlyInstallmentAmount = $totalRepayableAmount / $months;
            $totalUpfrontPayment = $downPaymentAmount + $processingFeeAmount;

            $loanApplication->loan_amount = $loanAmount;
            $loanApplication->product_id = $request->input('product_id');
            $loanApplication->loan_duration_id = $request->input('loan_duration_id');
            $loanApplication->save();


            // Store the loan application product record
            $loanApplicationProduct = LoanApplicationProduct::create([
                'request_for' => $request->input('request_for'),
                'loan_application_id' => $loanApplication->id,
                'product_id' => $request->input('product_id'),
                'loan_duration_id' => $request->input('loan_duration_id'),
                'loan_amount' => $loanAmount,
                'down_payment_percentage' => $downPaymentPercentage,
                'processing_fee_percentage' => $processingFeePercentage,
                'interest_rate_percentage' => $interestRatePercentage,
                'financed_amount' => $financedAmount,
                'processing_fee_amount' => round($processingFeeAmount, 2),
                'down_payment_amount' => round($downPaymentAmount, 2),
                'total_upfront_payment' => round($totalUpfrontPayment, 2),
                'disbursement_amount' => round($disbursementAmount, 2),
                'total_interest_amount' => round($totalInterestAmount, 2),
                'total_repayable_amount' => round($totalRepayableAmount, 2),
                'monthly_installment_amount' => round($monthlyInstallmentAmount, 2),
            ]);

            DB::commit();

            if ($loanAmount < env('MIN_AMOUNT')) {
                $loanApplication->status = 'rejected';
                $loanApplication->is_completed = 1;
                $loanApplication->is_submitted = 1;
                $loanApplication->save();

                return $this->sendError('Loan Application Rejected', ['error' => 'Loan Amount is less than 50,000']);


            }

            // Return a successful response
            return $this->sendResponse(
                $loanApplicationProduct,
                'Loan Application Product submitted successfully.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred.', ['error' => $e->getMessage()]);
        }
    }


    public function storeAmountAndDurationAfterCalculation(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'loan_amount' => 'required|numeric',
            'months' => 'required|integer|exists:loan_durations,value', // Ensure 'months' corresponds to loan duration in the DB
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            // Get the authenticated user's ID
            $userID = auth()->user()->id;
            $applicationID = $request->id;

            // Check if the user already has a submitted loan application
            $existingLoanApplication = LoanApplication::
            where('user_id', $userID)
                ->where('is_submitted', 1)
                ->where('id', $applicationID)
                ->first(); // Get the first existing loan application

            // Get the loan duration based on the months provided
            $loanDuration = LoanDuration::where('value', $request->months)->first();

            // If there is an existing loan application, return an error
            if ($existingLoanApplication) {
                return $this->sendError('An application is already in progress. A new application cannot be submitted.');
            }

            // Otherwise, update or create a loan application
            $newLoanApplication = LoanApplication::updateOrCreate(
                [
                    'id' => $applicationID,
                    'user_id' => $userID,
                    'is_submitted' => 0, // We only allow creating/updating if the loan is not yet submitted
                ],
                [
                    'loan_amount' => $request->loan_amount,
                    'loan_duration_id' => $loanDuration->id,
                    'is_submitted' => true,
                ]
            );

            DB::commit();

            // Return a successful response
            return $this->sendResponse(
                ['loan_application' => new LoanApplicationResource($newLoanApplication)],
                'Loan Application submitted successfully.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred.', ['error' => $e->getMessage()]);
        }
    }

    public function approveLoan($loanApplicationId)
    {
        DB::beginTransaction(); // Start a transaction

        try {
            // Fetch the loan application
            $loanApplication = LoanApplication::findOrFail($loanApplicationId);

            // Ensure the loan application has not already been approved
            if ($loanApplication->status === 'accepted') {
                return redirect()->back()->with('error', 'This loan application has already been accepted.');
            }

            // Set loan amount and duration for calculation
            $loanAmount = $loanApplication->loan_amount;
            $loanDuration = $loanApplication->loanDuration->value;

            // Call calculateLoan to get loan details
            $loanApplication->load('calculatedProduct');
            $loanDetails = $loanApplication->calculatedProduct;

            // Mark the loan as approved
            $loanApplication->status = 'accepted';
            $loanApplication->approved_by = auth()->id(); // Approved by the logged-in user
            $loanApplication->save();

            // Insert calculated loan details into the installments table
            $installmentData = [
                'loan_application_id' => $loanApplication->id,
                'user_id' => $loanApplication->user_id,
                'total_amount' => $loanDetails->total_repayable_amount,
                'monthly_installment' => $loanDetails->monthly_installment_amount,
                'processing_fee' => $loanDetails->processing_fee_amount,
                'total_markup' => $loanDetails->total_interest_amount,
                'approved_by' => auth()->id(),
            ];
            $installment = Installment::create($installmentData);

            // Generate individual monthly installments
            $startDate = now();
            for ($i = 1; $i <= $loanDuration; $i++) {
                $dueDate = $startDate->copy()->addMonths(1); // Calculate the due date
                InstallmentDetail::create([
                    'installment_id' => $installment->id,
                    'issue_date' => $startDate,
                    'due_date' => $dueDate,
                    'status' => 'unpaid',
                    'amount_due' => $loanDetails->monthly_installment_amount,
                ]);
                $startDate = $dueDate->copy()->addDay(); // Update startDate for the next installment
            }
            LogActivity::addToLog('Loan Application  ' . $loanApplication->application_id . ' Approved');

            DB::commit(); // Commit the transaction

            return redirect()->route('get-all-loan-applications')->with('success', 'Loan Application status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on failure

            // Log the exception for debugging purposes
            Log::error('Error approving loan application: ', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to approve loan application. Please try again.');
        }
    }

    public function getDashboardData()
    {
        try {
            $authUser = auth()->user();

            // Retrieve the loan application with its latest installment
            $loanApplication = LoanApplication::with('getLatestInstallment.details')->where('is_completed', 0)
                ->where('user_id', $authUser->id)
                ->first();

            if (!$loanApplication) {
                return $this->sendResponse(['is_application' => 'no loan'], 'No loan applications found.');
            }

            if ($loanApplication && $loanApplication->status == 'pending') {
                return $this->sendResponse(['is_application' => 'pending'], 'Your loan application has been submitted successfully and is under review.');
            }

            $installment = $loanApplication->getLatestInstallment;


            $details = InstallmentDetail::where('installment_id', $installment->id)->get();


            $totalAmount = $loanApplication->loan_amount;
            $paidLoans = 0;
            $paidInstallments = [];
            $unpaidInstallments = [];

            foreach ($details as $detail) {
                if ($detail->is_paid) {
                    $paidLoans += $detail->amount_due;
                }
            }

            $remainingLoans = $totalAmount - $paidLoans;

//            $latestPaid = [];
//            $nextUnpaid = [];

            // Retrieve the latest paid installment
            $latestPaid = InstallmentDetail::where('is_paid', 1)
                ->orderBy('paid_at', 'desc') // Primary sort by payment date
                ->orderBy('id', 'desc')      // Secondary sort by ID
                ->first();


            // Retrieve the next unpaid installment
            $nextUnpaid = InstallmentDetail::where('is_paid', 0)
                ->where('due_date', '>', now())
                ->orderBy('due_date', 'asc')
                ->first();


            return $this->sendResponse([
                'totalAmount' => round($totalAmount),
                'paidLoans' => round($paidLoans),
                'remainingLoans' => round($remainingLoans),
                'paidInstallmentsCount' => count($paidInstallments),
                'unpaidInstallmentsCount' => count($unpaidInstallments),
                'latestPaid' => $latestPaid ? new InstallmentDetailResource($latestPaid) : [],
                'nextUnpaid' => $nextUnpaid ? new InstallmentDetailResource($nextUnpaid) : [],
                'upcomingInstallments' => $this->getUpcomingInstallments(),
                'installmentHistory' => $this->getInstallmentHistory(),
                'allInstallments' => $this->getAllInstallments(),
                'lateFeeSummary' => $this->getLateFeeSummary(),
                'is_application' => 'approved'

            ], 'Loan data retrieved successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return error response
            return $this->sendError($e->getMessage());
        }
    }


    public function getUpcomingInstallments()
    {
        try {
            $authUser = auth()->user();

            $unpaidInstallments = Installment::with('details')
                ->where('user_id', $authUser->id)
                ->get()
                ->flatMap->details
                ->where('is_paid', 0)
                ->sortBy('due_date');

            return InstallmentDetailResource::collection($unpaidInstallments);
        } catch (\Exception $e) {
            Log::error('Upcoming Installments Retrieval Error: ' . $e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }

    public function getInstallmentHistory()
    {
        try {
            $authUser = auth()->user();

            // Fetch paid installments that have corresponding entries in the recoveries table
            $paidInstallments = InstallmentDetail::where('is_paid', 1)
                ->whereHas('recovery') // Ensure there is a corresponding recovery record
                ->with(['recovery', 'installment']) // Eager load related data
                ->whereHas('installment', function ($query) use ($authUser) {
                    $query->where('user_id', $authUser->id);
                })
                ->orderBy('due_date', 'desc')
                ->get();

            return InstallmentDetailResource::collection($paidInstallments);
        } catch (\Exception $e) {
            Log::error('Installment History Retrieval Error: ' . $e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }


    public function getAllInstallments()
    {
        try {
            $authUser = auth()->user();

            // Fetch all installment details for the authenticated user
            $installments = InstallmentDetail::with(['recovery', 'installment']) // Load recovery and installment relationships
            ->whereHas('installment', function ($query) use ($authUser) {
                $query->where('user_id', $authUser->id);
            })
                ->orderBy('due_date', 'asc') // Optional: Order by due date
                ->get();

            return InstallmentDetailResource::collection($installments);
        } catch (\Exception $e) {
            Log::error('All Installments Retrieval Error: ' . $e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }


    public function getLateFeeSummary()
    {
        try {
            $authUser = auth()->user();

            $lateFeePerDay = env('LATE_FEE', 0);

            $lateFeeData = Installment::with('details')
                ->where('user_id', $authUser->id)
                ->get()
                ->flatMap->details
                ->where('is_paid', 0)
                ->where('due_date', '<', now())
                ->map(function ($installment) use ($lateFeePerDay) {
                    $daysDelayed = now()->diffInDays($installment->due_date);
                    $daysDelayed = abs(round($daysDelayed));

                    $totalLateFee = $daysDelayed * $lateFeePerDay;
                    $totalLateFee = abs(round($totalLateFee, 2));

                    return [
                        'id' => $installment->id,
                        'installment_number' => $installment->installment_number,
                        'due_date' => $installment->due_date,
                        'amount_due' => $installment->amount_due,
                        'daysDelayed' => $daysDelayed,
                        'perDayLateFee' => $lateFeePerDay,
                        'totalLateFee' => $totalLateFee,
                        'totalAfterLateFee' => round($installment->amount_due + $totalLateFee, 2),
                    ];
                });

            return $lateFeeData->values();
        } catch (\Exception $e) {
            Log::error('Late Fee Summary Retrieval Error: ' . $e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Format an integer as an ordinal (e.g., 1st, 2nd, 3rd, etc.).
     *
     * @param int $number
     * @return string
     */
    private function formatOrdinal($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        $mod = $number % 100;
        return $number . ($suffixes[($mod - 20) % 10] ?? $suffixes[$mod] ?? 'th');
    }

    public function destroy(Request $request)
    {
        $loanApplication = LoanApplication::find($request->id);
        $loanApplication->update(['application_id' => 0]);
        $loanApplication->delete();

        return response()->json(['success' => 'Loan Application Deleted Successfully']);
    }

}
