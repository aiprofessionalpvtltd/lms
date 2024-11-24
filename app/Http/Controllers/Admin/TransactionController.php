<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
        ]);

        // Assign default values
        $request->merge([
            'payment_method' => 'Bank',
            'remarks' => 'Testing',
        ]);

        DB::beginTransaction(); // Start the transaction

        try {
            // Find the loan application and include the latest installment
            $loanApplication = LoanApplication::with('getLatestInstallment.details')->findOrFail($request->loan_application_id);

            // Calculate the disbursement amount
            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;

            // Create the transaction
            $transaction = Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(), // Authenticated user's ID
                'amount' => $disburseAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending', // Initially set to pending
                'remarks' => $request->remarks,
            ]);

            // Simulate payment processing
            $transaction->update([
                'status' => 'completed', // Set status to completed
                'transaction_reference' => 'REF-' . time(), // Mock transaction reference
            ]);

            // Retrieve all installment details for the loan application
            $installments = $loanApplication->getLatestInstallment->details;

            if ($installments->isEmpty()) {
                throw new \Exception('No installments found for this loan application.');
            }

            // Initialize the start date
            $startDate = Carbon::now();

             // Update installments with new dates
            foreach ($installments as $installment) {
                $dueDate = $startDate->copy()->addMonths(1);

                $installment->update([
                    'issue_date' => $startDate,
                    'due_date' => $dueDate,
                ]);

                $startDate = $dueDate->copy()->addDay(); // Start date for next installment is 1 day after due date
            }

            DB::commit(); // Commit the transaction

            return redirect()->route('show-installment')->with('success', 'Transaction and installments updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction on error

            dd( $e->getMessage());
            // Return an error response
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

}
