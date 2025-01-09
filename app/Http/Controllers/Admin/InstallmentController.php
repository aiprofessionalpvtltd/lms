<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;

use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-installments'])->only(['index', 'show', 'view']);
        $this->middleware(['permission:create-installments']);
        $this->middleware(['permission:edit-installments'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-installments'])->only('destroy');
    }

    public function index()
    {
        $title = 'installments';
        $installments = Installment::all();
        LogActivity::addToLog('installments Listing View');

        return view("admin.installment.index", compact('installments', 'title'));
    }

//    public function view($id)
//    {
//        $installment = Installment::with(['details', 'user', 'loanApplication' ,'loanApplication.calculatedProduct','recoveries'])->findOrFail($id);
////        dd($installment);
//        return view("admin.installment.view", compact('installment'));
//    }


    public function view($id)
    {
        // Retrieve the installment with all relations
        $installment = Installment::with([
            'details',
            'user',
            'loanApplication',
            'loanApplication.calculatedProduct',
            'recoveries'
        ])->findOrFail($id);

        // Define ERC percentages
        $ercWithin3Months = 10; // 10% within 3 months
        $ercAfter3Months = 5;   // 5% after 3 months

        // Filter only unpaid installments
        $unpaidInstallments = $installment->details->filter(function ($detail) {
            return $detail->is_paid == 0; // Only unpaid installments
        });

        // Initialize total remaining loan with the sum of unpaid installment amounts
        $remainingLoan = $unpaidInstallments->sum('amount_due');

//        dd($remainingLoan);
        // Map the unpaid installments with cumulative remaining loan and ERC
        $unpaidInstallmentsWithERC = $unpaidInstallments->map(function ($detail, $index) use ($ercWithin3Months, $ercAfter3Months, &$remainingLoan, $unpaidInstallments) {

            // Apply ERC logic based on the installment index (for non-last installments)
            $penaltyPercentage = ($index < 3) ? $ercWithin3Months : $ercAfter3Months;
            $penaltyAmount = ($remainingLoan * $penaltyPercentage) / 100;

            // Ensure remaining loan and total payable don't go below 0
            $remainingLoan = max($remainingLoan, 0); // Ensure remaining loan is non-negative
            $totalPayable = $remainingLoan + $penaltyAmount;

            // Add calculated fields to the detail object
            $detail->remaining_loan = $remainingLoan;
            $detail->penalty_percentage = $penaltyPercentage;
            $detail->penalty_amount = $penaltyAmount;
            $detail->total_payable = max($totalPayable, 0); // Ensure total_payable doesn't go negative

            return $detail;
        });

        LogActivity::addToLog('installments of loan application '.$installment->loanApplication->application_id.' View');


        // Return to the view with calculated unpaid installments
        return view('admin.installment.view', [
            'installment' => $installment,
            'unpaidInstallments' => $unpaidInstallmentsWithERC
        ]);
    }

    public function updateDueDate(Request $request, $id)
    {
        $request->validate([
            'due_date' => 'required|date',
        ]);
        $installmentDetail = InstallmentDetail::findOrFail($id);
        $installmentDetail->due_date = $request->due_date;
        $installmentDetail->save();
        LogActivity::addToLog('installments due date of  '.$installmentDetail->installment->loanApplication->application_id.' Updated');

        return response()->json(['message' => 'Due date updated successfully.'], 200);
    }

    public function updateIssueDate(Request $request, $id)
    {
        $request->validate([
            'issue_date' => 'required|date',
        ]);
        $installmentDetail = InstallmentDetail::findOrFail($id);
        $installmentDetail->issue_date = $request->issue_date;
        $installmentDetail->save();
        LogActivity::addToLog('installments issue date of  '.$installmentDetail->installment->loanApplication->application_id.' Updated');

        return response()->json(['message' => 'Issue date updated successfully.'], 200);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $installment = Installment::findOrFail($request->id);

            $loanApplication = LoanApplication::findOrFail($installment->loan_application_id);
            $loanApplication->status = 'pending';
            $loanApplication->save();

            $installment->delete();

            DB::commit();

            return response()->json(['success' => 'Installment Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'An error occurred while deleting the installment.', 'message' => $e->getMessage()], 500);
        }
    }


}
