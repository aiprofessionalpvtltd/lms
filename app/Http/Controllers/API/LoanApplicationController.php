<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanApplicationResource;
use App\Http\Resources\LoanApplicationTrackingResource;
use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\LoanApplicationHistory;
use App\Models\LoanApplicationProduct;
use App\Models\LoanAttachment;
use App\Models\LoanDuration;
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

    public function calculateLoan(Request $request)
    {
        $loanAmount = $request->input('loan_amount');
        $months = $request->input('months');
        $requestType = $request->input('request_for');
        $downPaymentPercentage = $request->input('down_payment_percentage', 10); // Default to 10% if not specified

        // Validate input
        if (!in_array($months, [3, 6, 9, 12])) {
            return response()->json(['error' => 'Invalid month duration. Choose from 3, 6, 9, or 12 months.'], 400);
        }

        if ($requestType === 'product') {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['error' => 'Product not found.'], 404);
            }

            $productProcessingFeePercentage = $product->processing_fee;
            $productInterestRate = $product->interest_rate;

            $processingFeeRate = $productProcessingFeePercentage / 100;
            $interestRate = $productInterestRate / 100;

            $downPayment = $loanAmount * ($downPaymentPercentage / 100);
            $processingFeeAmount = $loanAmount * $processingFeeRate;
            $totalUpfrontPayment = $downPayment + $processingFeeAmount;
            $financedAmount = $loanAmount - round($downPayment);
            $disbursementAmount = $financedAmount - $processingFeeAmount;

            $totalInterestAmount = $financedAmount * $interestRate;
            $totalRepayableAmount = $financedAmount + $totalInterestAmount;
            $monthlyInstallmentAmount = $totalRepayableAmount / $months;

        } elseif ($requestType === 'loan') {
            $standardProcessingFeePercentage = env('STANDARD_PROCESSING_FEE');
            $standardInterestRate = env('STANDARD_INTEREST');
            $downPaymentPercentage = 0;
            $totalUpfrontPayment = 0;
            $productId = NULL;

            $processingFeeRate = $standardProcessingFeePercentage / 100;
            $interestRate = $standardInterestRate / 100;

            $downPayment = $loanAmount * ($downPaymentPercentage / 100);
            $financedAmount = $loanAmount - round($downPayment);
            $processingFeeAmount = $financedAmount * $processingFeeRate;
            $disbursementAmount = $financedAmount - $processingFeeAmount;

            $totalInterestAmount = $financedAmount * $interestRate;
            $totalRepayableAmount = $financedAmount + $totalInterestAmount;
            $monthlyInstallmentAmount = $totalRepayableAmount / $months;
        } else {
            return response()->json(['error' => 'Invalid request_for value.'], 400);
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

                return view('admin.loan_applications.index', compact('loanApplications'));
            }

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
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


                return view('admin.loan_applications.view', compact('loanApplication', 'toUsers', 'loanApplicationProduct'));
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
            $loanApplication = LoanApplication::where([
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
            'loan_amount' => 'sometimes|numeric',
            'product_id' => 'sometimes|exists:products,id',
            'loan_duration_id' => 'sometimes|exists:loan_durations,id',
            'product_service_id' => 'required|exists:product_services,id',
            'loan_purpose_id' => 'required|exists:loan_purposes,id',
            'address' => 'required|string',
            'reference_contact_1' => 'required|string|max:255',
            'reference_contact_2' => 'required|string|max:255',
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

            $runningLoanApplication = LoanApplication::where('user_id', $userID)->where('is_completed', 0)->count();

            if ($runningLoanApplication > 0) {
                return $this->sendError('An application is already in progress. A new application cannot be submitted.');
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

//            dd($toUsers);
            if (!$toUsers) {

                $toUsers = User::whereHas('roles', function ($query) use ($roleId, $provinceID, $districtID, $cityID) {
                    $query->where('id', 2);
                })->first();

            }

            $toRoleID = $toUsers->roles->first()->id;

            $loanApplication = LoanApplication::create([
                'name' => $request->name,
                'email' => $request->email,
                'loan_amount' => $request->loan_amount,
                'product_id' => $request->product_id,
                'loan_duration_id' => $request->loan_duration_id,
                'product_service_id' => $request->product_service_id,
                'loan_purpose_id' => $request->loan_purpose_id,
                'user_id' => $userID,
                'address' => $request->address,
                'reference_contact_1' => $request->reference_contact_1,
                'reference_contact_2' => $request->reference_contact_2,
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
            'signature' => 'required|string',  // Expecting base64 string for signature
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

            // Handle bank document upload
            $bankDocumentPath = $request->bank_document->store('documents', 'public');
            LoanAttachment::updateOrCreate(
                [
                    'loan_application_id' => $loanApplication->id,
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
                    'document_type_id' => 3,  // Type ID for Signature
                ],
                [
                    'path' => $signaturePath,
                ]
            );

            DB::commit();
            return $this->sendResponse([
                'loan_application' => new LoanApplicationResource($loanApplication)
            ], 'Documents uploaded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong: ' . $e->getMessage());
        }
    }

    public function completeApplication($id)
    {


        $loanApplication = LoanApplication::find($id);

        if (!$loanApplication) {
            return redirect()->back()->with('error', 'Loan Application not found.');
        }

        $loanApplication->is_completed = true;
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

            $loanApplication->loan_amount =$loanAmount;
            $loanApplication->product_id =$request->input('product_id');
            $loanApplication->loan_duration_id =$request->input('loan_duration_id');
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


//        $loanDetails = $this->calculateLoan(new Request(['loan_amount' => $loanAmount, 'months' => $loanDuration]))->getData(true);
//        $loanDetails = $loanDetails['data'];

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
            InstallmentDetail::create([
                'installment_id' => $installment->id,
                'due_date' => $startDate->copy()->addMonths($i),
                'amount_due' => $loanDetails->monthly_installment_amount,
            ]);
        }
        return redirect()->route('get-all-loan-applications')->with('success', 'Loan Application status updated successfully.');

    }

    public function getDashboardData(Request $request)
    {
        try {
            $authUser = auth()->user();

            // Fetch total loan amount for the authenticated user
            $totalLoans = Installment::where('user_id', $authUser->id)->sum('total_amount');

            // Calculate paid loans by summing up all paid installments
            $paidLoans = InstallmentDetail::whereHas('installment', function ($query) use ($authUser) {
                $query->where('user_id', $authUser->id);
            })
                ->where('is_paid', 1)
                ->sum('amount_due');

            // Calculate remaining loans by subtracting paid loans from total loans
            $remainingLoans = $totalLoans - $paidLoans;

            // Retrieve all installments with payment status for the authenticated user
            $installments = InstallmentDetail::whereHas('installment', function ($query) use ($authUser) {
                $query->where('user_id', $authUser->id);
            })
                ->with(['installment' => function ($query) {
                    $query->select('id', 'user_id', 'total_amount');
                }])
                ->select(
                    '*',
                    DB::raw("CASE WHEN is_paid = 1 THEN updated_at ELSE NULL END as payment_date")
                )
                ->orderBy('due_date')
                ->get();

            // Group installments by paid and unpaid status
            $paidInstallments = $installments->where('is_paid', 1);
            $unpaidInstallments = $installments->where('is_paid', 0);

            // Get the latest paid installment
            $lastPaidInstallment = $paidInstallments->sortByDesc('due_date')->first();

            // Get the next unpaid installment based on the due date, excluding paid installments
            $nextUpcomingInstallment = $unpaidInstallments->filter(function ($installment) {
                return $installment->due_date > now();
            })->first();

            // Collect both last paid (if exists) and next unpaid into latestUpcomingInstallments
            $latestUpcomingInstallments = collect([$lastPaidInstallment, $nextUpcomingInstallment])
                ->filter(function ($installment) {
                    return $installment && $installment->is_paid == 0; // Skip if `is_paid` is 1
                });

            // Filter for past installments (installment history)
            $installmentHistory = $paidInstallments;

            // Late fee calculations
            $lateFeePerDay = 200; // PKR per day late fee
            $lateFeeData = [];

            foreach ($unpaidInstallments as $installment) {
                if ($installment->due_date < now()) {  // Only calculate if past due date
                    $daysDelayed = abs(now()->diffInDays($installment->due_date));
                    $totalLateFee = $daysDelayed * $lateFeePerDay;
                    $totalAfterLateFee = $installment->amount_due + $totalLateFee;

                    $lateFeeData[] = [
                        'id' => $installment->id,
                        'due_date' => $installment->due_date,
                        'amount_due' => $installment->amount_due,
                        'daysDelayed' => round($daysDelayed),
                        'perDayLateFee' => round($lateFeePerDay),
                        'totalLateFee' => round($totalLateFee),
                        'totalAfterLateFee' => round($totalAfterLateFee),
                    ];
                }
            }

            // Return loan data, summary information, and all installments, including late fee data
            return $this->sendResponse([
                'totalLoans' => round($totalLoans),
                'paidLoans' => round($paidLoans),
                'remainingLoans' => round($remainingLoans),
                'paidInstallments' => $paidInstallments->count(),
                'unpaidInstallments' => $unpaidInstallments->count(),
                'upcomingInstallments' => $unpaidInstallments->values(),
                'latestUpcomingInstallments' => $latestUpcomingInstallments->values(),
                'installmentHistory' => $installmentHistory->values(),
                'allInstallments' => $installments,
                'lateFeeSummary' => $lateFeeData  // Late fee details for overdue installments
            ], 'Loan data retrieved successfully.');

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }

}
