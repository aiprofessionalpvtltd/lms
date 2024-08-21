<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanApplicationResource;
use App\Models\LoanApplication;
use App\Models\LoanApplicationHistory;
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
        $status = $request->get('status', 'pending');

        try {
            // Fetch loan applications based on the status
            $loanApplications = LoanApplication::where('status', $status)->get();

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
            'documents.*' => 'sometimes|file|mimes:pdf,jpg,png,doc,docx|max:2048', // Example validation rules
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        DB::beginTransaction();

        try {

            $documents = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $documents[] = $file->store('documents', 'public');
                }
            }

            $userID = auth::user()->id;

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
                'documents' => $documents,
            ]);

            LoanApplicationHistory::create([
                'loan_application_id' => $loanApplication->id,
                'status' => 'pending',
            ]);


            DB::commit();
            return $this->sendResponse([
                'loan_application' => new LoanApplicationResource($loanApplication)
            ], 'Loan Application Submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
