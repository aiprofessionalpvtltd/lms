<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanApplicationResource;
use App\Http\Resources\LoanDurationResource;
use App\Http\Resources\RoleResource;
use App\Models\Guarantor;
use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuarantorController extends BaseController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_application_id' => 'required|exists:loan_applications,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cnic_no' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:15',
            'cnic_attachment' => 'required|file|mimes:jpg,png,pdf',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        // Begin database transaction
        DB::beginTransaction();

        try {

            $existingGuarantor = Guarantor::where('loan_application_id', $request->loan_application_id)->count();

            if ($existingGuarantor == 2) {
                return $this->sendError('You can submit a maximum of 2 guarantors.');
            }

            $cnicAttachmentPath = '';
            if ($request->file('cnic_attachment')) {
                // Store the CNIC attachment
                $cnicAttachmentPath = $request->file('cnic_attachment')->store('guarantors', 'public');

            }

            $loanApplication = LoanApplication::find($request->loan_application_id);

            // Check if any loan applications are found
            if ($loanApplication == null) {
                return $this->sendError('No Loan Applications found');
            }

            $loanApplication->is_submitted = true;
            $loanApplication->save();

            $guarantor = Guarantor::create([
                'loan_application_id' => $loanApplication->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'cnic_no' => $request->cnic_no,
                'address' => $request->address,
                'mobile_no' => $request->mobile_no,
                'cnic_attachment' => $cnicAttachmentPath,
            ]);


            // Commit the transaction
            DB::commit();

            return $this->sendResponse(
                new LoanApplicationResource($loanApplication),
                'Guarantor added successfully'
            );
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();

            // Return error response
            return response()->json([
                'error' => 'Failed to add guarantor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $guarantor = Guarantor::findOrFail($id);
        return response()->json(['data' => $guarantor], 200);
    }
}
