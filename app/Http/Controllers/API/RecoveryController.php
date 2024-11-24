<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Resources\LoanApplicationResource;
use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\Recovery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecoveryController extends BaseController
{


    public function instalmentRecovery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'installment_detail_id' => 'required|exists:installment_details,id',
            'payment_method' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $installmentDetail = InstallmentDetail::find($request->installment_detail_id);

            $amountDue = $installmentDetail->amount_due;
            // Initialize penalty fee
            $penaltyFee = 0;
            $overdueDays = 0;

            // Calculate penalty if the due date has passed
            if (Carbon::now()->greaterThan($installmentDetail->due_date)) {
                $overdueDays = ((Carbon::now()->diffInDays($installmentDetail->due_date)));
                $overdueDays = abs($overdueDays);
                $penaltyFee = (($overdueDays * env('LATE_FEE'))); // Calculate penalty at 250 per day
                $penaltyFee = abs($penaltyFee);
            }

            $recoveryDetail = [
                'installment_detail_id' => $installmentDetail->id,
                'installment_id' => $installmentDetail->installment_id,
                'amount' => $amountDue,
                'overdue_days' => round($overdueDays),
                'penalty_fee' => round($penaltyFee), // New field for penalty fee
                'total_amount' => round($amountDue + $penaltyFee), // Total amount including penalty
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'remarks' => $request->remarks,
            ];

            // Save recovery record, including penalty fee
            Recovery::create($recoveryDetail);


            // Update installment detail
            $installmentDetail->is_paid = true;
            $installmentDetail->amount_paid = $installmentDetail->amount_due;
            $installmentDetail->paid_at = currentDateInsert();
            $installmentDetail->save();

            // Check if all installment details are paid
            $installment = Installment::find($installmentDetail->installment_id);
            $allPaid = $installment->details()->where('is_paid', false)->count() === 0;

            if ($allPaid) {
                $loanApplication = LoanApplication::find($installment->loan_application_id);
                $loanApplication->is_completed = true;
                $loanApplication->save();
            }

            DB::commit();

            // Return the loan applications as a response
            return $this->sendResponse([] , 'Installment Recovered successfully.' );


        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                $e->getMessage()
            );
        }
    }
}
