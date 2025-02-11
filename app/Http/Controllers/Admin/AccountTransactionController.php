<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\AccountVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-accounts'])->only(['index', 'show']);
        $this->middleware(['permission:create-accounts'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-accounts'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Account Journal Entry';
        $transactions = AccountTransaction::with('vendorAccount')->get();
        return view('admin.account-transaction.index', compact('title', 'transactions'));
    }
    public function getHistoryByAccountID($id)
    {
        $title = 'General Ledger';
        $transactions = AccountTransaction::where('account_id',$id)->get();
        return view('admin.account-transaction.history_by_account_id', compact('title', 'transactions'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Account Journal Entry';
        $accounts = Account::all();
        $vendorAccounts = AccountVendor::all();
        return view('admin.account-transaction.create', compact('title', 'accounts','vendorAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|array',
            'account_id.*' => 'exists:accounts,id',
            'vendor_account_id' => 'required|array',
            'vendor_account_id.*' => 'nullable|exists:account_vendors,id',
            'date' => 'required|date',
            'debit_amount' => 'required|array',
            'debit_amount.*' => 'numeric|min:0',
            'credit_amount' => 'required|array',
            'credit_amount.*' => 'numeric|min:0',
            'reference' => 'required|array',
            'reference.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.*' => 'nullable|string',
        ]);
//        dd($validator->errors());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction(); // Start DB transaction

        try {
            // Loop through each account and process the journal entry
            foreach ($request->account_id as $index => $accountId) {
                $account = Account::findOrFail($accountId); // Get account details
                $debitAmount = $request->debit_amount[$index] ?? 0;
                $creditAmount = $request->credit_amount[$index] ?? 0;
                $reference = $request->reference[$index] ?? null;
                $description = $request->description[$index] ?? null;
                $vendorAccountId = $request->vendor_account_id[$index] ?? null;

                // Determine if account is credit or debit based on its account type
                $accountType = $account->accountType;
//                dd($accountType);
                if ($accountType->is_credit) {
                    // If account is credit, subtract debit amount, add credit amount
                    $account->balance -= $debitAmount;
                    $account->balance += $creditAmount;
                } elseif ($accountType->is_debit) {
                    // If account is debit, add debit amount, subtract credit amount
                    $account->balance += $debitAmount;
                    $account->balance -= $creditAmount;
                }

                 // Save updated balance
                $account->save();

                // Store transaction entry
                AccountTransaction::create([
                    'account_id' => $account->id,
                    'vendor_account_id' => $vendorAccountId,
                    'date' => $request->date,
                    'debit' => $debitAmount,
                    'credit' => $creditAmount,
                    'reference' => $reference,
                    'transaction_type' => 'Journal Entry',
                    'description' => $description,
                ]);
            }

            DB::commit(); // Commit transaction if all operations succeed

            return redirect()->route('show-account-transaction')->with('success', 'Journal Entries recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Journal Entry Error: ' . $e->getMessage()); // Log the error

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Account Journal Entry Details';
        $transaction = AccountTransaction::find($id);
        return view('admin.account-transaction.show', compact('title', 'transaction '));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Account Journal Entry';
        $transaction = AccountTransaction::find($id);
        return view('admin.account-transaction.edit', compact('title', 'transaction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'credit_debit' => 'required|in:credit,debit',
            'reference' => 'nullable|string|max:255',
            'transaction_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $transaction = AccountTransaction::find($id);

        if (!$transaction) {
            return redirect()->route('show-account-transaction')->with('error', 'Transaction not found.');
        }

        // Determine Credit or Debit
        $credit = $request->input('credit_debit') === 'credit' ? $request->input('amount') : 0;
        $debit = $request->input('credit_debit') === 'debit' ? $request->input('amount') : 0;

        // Update transaction details
        $transaction->update([
            'account_id' => $request->input('account_id'),
            'date' => $request->input('date'),
            'debit' => $debit,
            'credit' => $credit,
            'reference' => $request->input('reference'),
            'transaction_type' => $request->input('transaction_type'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('show-account-transaction')->with('success', 'Journal Entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $transaction = AccountTransaction::find($request->id);
        $transaction->delete();

        return response()->json(['success' => 'Account Journal Entry deleted successfully.']);
    }
}
