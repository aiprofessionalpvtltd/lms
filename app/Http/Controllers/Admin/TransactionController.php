<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
        ]);

        $request->payment_method = 'Bank';
        $request->remarks = 'Testing';
        // Find the loan application to ensure it's valid and can be paid
        $loanApplication = LoanApplication::with('getLatestInstallment')->findOrFail($request->loan_application_id);

        $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;

        // Create the transaction
        $transaction = Transaction::create([
            'loan_application_id' => $loanApplication->id,
            'user_id' => Auth::id(), // Get the authenticated user's ID
            'amount' => $disburseAmount,
            'payment_method' => $request->payment_method,
            'status' => 'pending', // Initially set to pending
            'remarks' => $request->remarks,
        ]);

        // Here you can call an API to process the payment if needed
        // For now, we'll just simulate the payment process

        // Simulate payment processing (replace this with actual payment API integration)
        // Assume payment is successful
        $transaction->status = 'completed'; // Set the status to completed
        $transaction->transaction_reference = 'REF-' . time(); // Set a mock transaction reference
        $transaction->save();

        // Optionally, you can update the loan application status or balance here

        return redirect()->route('show-installment')->with('success', 'Transaction created successfully.');


    }
}
