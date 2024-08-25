<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanApplicationResource;
use App\Models\LoanApplication;
use App\Models\LoanApplicationHistory;
use App\Models\LoanAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends BaseController
{
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
            return $this->sendError('An error occurred while retrieving loan applications. Please try again later.');
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

                return view('admin.loan_applications.view', compact('loanApplication', 'toUsers'));
            }


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred while retrieving loan applications. Please try again later.');
        }
    }


    public function getUserData(Request $request)
    {
        // Get the status from the request, defaulting to 'pending' if not provided
        $status = $request->get('status', 'pending');
        $userID = auth()->user()->id;

        try {
            // Fetch loan applications based on the status
            $loanApplications = LoanApplication::where('status', $status)->where('user_id', $userID)->get();

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
            return $this->sendError('An error occurred while retrieving loan applications. Please try again later.');
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
            'bank_document' => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048',
            'salary_slip_document' => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048',
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


}
