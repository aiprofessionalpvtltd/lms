<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanApplicationResource;
use App\Models\LoanApplication;
use App\Models\LoanApplicationHistory;
use App\Models\LoanAttachment;
use App\Models\LoanDuration;
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


    public function calculateLoan(Request $request)
    {
        $loanAmount = $request->input('loan_amount');
        $months = $request->input('months');

        // Validate input
        if (!in_array($months, [3, 6, 9, 12])) {
            return response()->json(['error' => 'Invalid month duration. Choose from 3, 6, 9, or 12 months.'], 400);
        }

        // Constants
        $processingFeeRate = 0.027;
        $riskPremiumRate = 0.02;
        $operatingCostsRate = 0.03;
        $profitMarginRate = 0.20;
        $costOfFundsRate = $this->getCostOfFundsRate($months);

        // Calculate processing fee
        $processingFee = $loanAmount * $processingFeeRate;

        // Calculate total markup
        $totalMarkup = ($costOfFundsRate + $riskPremiumRate + $operatingCostsRate + $profitMarginRate) * ($months / 12) * $loanAmount;

        // Calculate total payable amount
        $totalPayableAmount = $loanAmount + $processingFee + $totalMarkup;

        // calculate overall markup
        $overallMarkup = $processingFee + $totalMarkup;

        // calculate Monthly Installment to Pay
        $monthlyInstallment = $totalPayableAmount / $months;

        return $this->sendResponse(
            [
                'loan_amount' => $loanAmount,
                'months' => $months,
                'processing_fee' => round($processingFee),
                'total_markup' => round($totalMarkup),
                'over_markup' => round($overallMarkup),
                'monthly_installment' => round($monthlyInstallment),
                'total_payable_amount' => round($totalPayableAmount),
            ],
            'Loan calculated successfully.'
        );


    }

    private function getCostOfFundsRate($months)
    {
        switch ($months) {
            case 6:
                return 0.05; // 5% for 6 months
            case 9:
                return 0.075; // 7.5% for 9 months
            case 12:
                return 0.10; // 10% for 12 months
            case 3:
                return 0.05; // 5% for 3 months
            default:
                return 0; // No cost of funds for 3 months
        }
    }



    public function getAllData(Request $request)
    {
        // Get the status from the request, defaulting to 'pending' if not provided
        $status = $request->get('status');

        try {
            $loanApplications = [];
            if ($status) {
                // Fetch loan applications based on the status
                $loanApplications = LoanApplication::where('status', $status)->get();
            } else {
                $loanApplications = LoanApplication::all();

            }


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

                $roleId = 4; // for loan onboarding

                $toUsers = User::where('id', '!=', auth()->user()->id)
                    ->whereHas('roles', function ($query) use ($roleId) {
                        $query->where('id', '>=', $roleId);
                    })->with('roles:name,id')->get();

                $loanApplication->load('loanDuration');
                $extraParameterForLoan = [
                    'loan_amount' => $loanApplication->loan_amount,
                    'months' =>$loanApplication->loanDuration->value
                ];

                $request->merge($extraParameterForLoan);

                $loanCalculator = $this->calculateLoan($request)->getData(true);

                $loanCalculatedDetail = $loanCalculator['data'];

                return view('admin.loan_applications.view', compact('loanApplication', 'toUsers' ,'loanCalculatedDetail'));
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
            if ($status) {
                // Fetch loan applications based on the status
                $loanApplications = LoanApplication::where('status', $status)->where('user_id', $userID)->get();
            } else {
                $loanApplications = LoanApplication::where('user_id', $userID)->get();

            }

            // Check if any loan applications are found
            if ($loanApplications->isEmpty()) {
                return $this->sendError('No Loan Applications found for the given status.');
            }

            // Return the loan applications as a response
            return $this->sendResponse(
                LoanApplicationResource::collection($loanApplications),
                'Loan Applications retrieved successfully.'
            );

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'loan_amount' => 'required|numeric',
            'loan_duration_id' => 'required|exists:loan_durations,id',
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

            $userID = auth::user()->id;
            $userRoleID = auth()->user()->roles->first()->id;

            $runningLoanApplication = LoanApplication::where('user_id',$userID)->where('is_submitted',1)->count();

              if($runningLoanApplication > 0){
                return $this->sendError('An application is already in progress. A new application cannot be submitted.');
            }

            $roleId = 4; // for Loan Onboarding

            $toUsers = User::whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
            })->first();

            if(!$toUsers){
                return $this->sendError('Loan Onboarding user not found');

            }

            $toRoleID = $toUsers->roles->first()->id;

            $loanApplication = LoanApplication::create([
                'name' => $request->name,
                'email' => $request->email,
                'loan_amount' => $request->loan_amount,
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

            // Check if the user already has a submitted loan application
            $existingLoanApplication = LoanApplication::where('user_id', $userID)
                ->where('is_submitted', 1)
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
                    'user_id' => $userID,
                    'is_submitted' => 0, // We only allow creating/updating if the loan is not yet submitted
                ],
                [
                    'loan_amount' => $request->loan_amount,
                    'loan_duration_id' => $loanDuration->id,
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


}
