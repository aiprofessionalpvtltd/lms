<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\Recovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecoveryController extends Controller
{
    public function create($installmentDetailId)
    {
        $title = 'Installment Recovery';
        $installmentDetails = InstallmentDetail::where('installment_id',$installmentDetailId)->where('is_paid' , false)->get();
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

            // Save recovery record
            Recovery::create([
                'installment_detail_id' => $installmentDetail->id,
                'installment_id' => $installmentDetail->installment_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'remarks' => $request->remarks,
            ]);

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
                ->with('success', 'Recovery payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while recording the recovery payment. Please try again.'])
                ->withInput();
        }
    }
}
