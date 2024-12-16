<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\Recovery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecoveryController extends Controller
{
    public function create($installmentDetailId)
    {
        $title = 'Installment Recovery';

        $installmentDetails = InstallmentDetail::where('installment_id', $installmentDetailId)
            ->where('is_paid', false)
            ->get()
            ->map(function ($detail) {
                // Calculate the number of overdue days
                $overdueDays = Carbon::now()->greaterThan($detail->due_date)
                    ? Carbon::now()->diffInDays($detail->due_date)
                    : 0;
                $overdueDays = abs($overdueDays);

                // Calculate the late fee (assuming 250 per day as the penalty fee)
                $lateFee = $overdueDays * env('LATE_FEE', 250); // Default to 250 if not set in .env
                $lateFee = abs($lateFee);

                // Calculate the total amount (amount due + late fee)
                $amountDue = round($detail->amount_due, 2); // Ensure two decimal places for consistency

                // Add these attributes to the installment detail
                $detail->overdue_days = $overdueDays; // No need for abs() since overdueDays cannot be negative
                $detail->late_fee = $lateFee;
                $detail->total_amount = $amountDue + $lateFee;

                return $detail;
            });
        return view('admin.recovery.create', compact('installmentDetails', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'installment_detail_id' => 'required|exists:installment_details,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

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
            $installmentDetail->amount_paid = $request->amount;
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
            return redirect()->route('show-installment')
                ->with('success', 'Recovery payment recorded successfully with penalty fee calculated.');
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while recording the recovery payment. Please try again.'])
                ->withInput();
        }
    }

    public function storeRecovery(Request $request)
    {
         $request->validate([
            'installment_detail_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'overdue_days' => 'required|string|max:255',
            'late_fee' => 'required|string|max:255',
            'total_amount' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $installmentDetail = InstallmentDetail::findOrFail($request->installment_detail_id);

            $penaltyFee = abs($request->overdue_days * config('app.late_fee', 250)); // Late fee from config

            $totalAmount = $request->amount + $penaltyFee;

             $recoveryData = [
                'installment_detail_id' => $installmentDetail->id,
                'installment_id' => $installmentDetail->installment_id,
                'amount' => $request->amount,
                'overdue_days' => abs($request->overdue_days),
                'penalty_fee' => $penaltyFee,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'remarks' => $request->remarks,
                'created_at' => \Carbon\Carbon::parse($request->date)->format('Y-m-d H:i:s')
            ];
            // Save recovery record
            $recovery = Recovery::insert($recoveryData);

            // Update installment detail
            $installmentDetail->update([
                'is_paid' => true,
                'amount_paid' => $request->amount,
                'paid_at' => now(),
            ]);

            // Check if all installment details are paid
            $installment = $installmentDetail->installment;
            if ($installment->details()->where('is_paid', false)->count() === 0) {
                $installment->loanApplication->update(['is_completed' => true]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Recovery payment recorded successfully.',
                'recovery' => $recovery,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while recording the recovery payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
