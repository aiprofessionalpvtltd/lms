<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $transactions = AccountTransaction::all();
        return view('admin.account-transaction.index', compact('title', 'transactions'));
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
        return view('admin.account-transaction.create', compact('title', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

        // Determine Credit or Debit
        $credit = $request->input('credit_debit') === 'credit' ? $request->input('amount') : 0;
        $debit = $request->input('credit_debit') === 'debit' ? $request->input('amount') : 0;

        // Store the transaction
        $transaction = AccountTransaction::create([
            'account_id' => $request->input('account_id'),
            'date' => $request->input('date'),
            'debit' => $debit,
            'credit' => $credit,
            'reference' => $request->input('reference'),
            'transaction_type' => $request->input('transaction_type'),
            'description' => $request->input('description'),
        ]);

        if ($transaction) {
            return redirect()->route('show-account-transaction')->with('success', 'Journal Entry recorded successfully.');
        } else {
            return redirect()->route('show-account-transaction')->with('error', 'Something went wrong.');
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
        return view('admin.account-transaction.show', compact('title', 'transaction'));
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
