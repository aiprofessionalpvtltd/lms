<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountName;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-accounts'])->only(['index', 'show']);
        $this->middleware(['permission:create-accounts'])->only(['create', 'store']);
        $this->middleware(['permission:edit-accounts'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-accounts'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Chart Of Accounts';
        $accounts = Account::with('accountName', 'accountType', 'parent.accountName')->get(); //
//        dd($accounts);
        return view('admin.account.index', compact('title', 'accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Chart Of  Account';
        $accounts = Account::all();
        $types = AccountType::all();
        $names = AccountName::all();
        return view('admin.account.create', compact('title', 'types', 'names', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // Debugging Request Data
        // dd($request->all());

        // Validation
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|unique:accounts,code',
            'account_name_id' => 'required|exists:account_names,id',
            'account_type_id' => 'required|exists:account_types,id',
            'parent_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate Code if not provided
        if (!$request->filled('code')) {
            $lastAccount = Account::latest('code')->first();
            $newCode = $lastAccount ? ((int)$lastAccount->code + 1) : 100;
        } else {
            $newCode = $request->input('code');
        }

        // Create Account
        $account = Account::create([
            'code' => $newCode,
            'account_name_id' => $request->input('account_name_id'),
            'account_type_id' => $request->input('account_type_id'),
            'parent_id' => $request->input('parent_id'),
        ]);

        if ($account) {
            return redirect()->route('show-account')->with('success', 'Account created successfully.');
        } else {
            return redirect()->route('show-account')->with('error', 'Something went wrong.');
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
        $title = 'Chart Of Account Details';
        $account = Account::with('category')->find($id); // Load the related category
        return view('admin.account.show', compact('title', 'account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Chart Of  Account';
        $account = Account::find($id);
        $accounts = Account::all();
        $types = AccountType::all();
        $names = AccountName::all();

        return view('admin.account.edit', compact('title', 'types', 'names', 'account', 'accounts'));
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
        // Validation
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|unique:accounts,code,' . $id,
            'account_name_id' => 'required|exists:account_names,id',
            'account_type_id' => 'required|exists:account_types,id',
            'parent_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Find Account
        $account = Account::findOrFail($id);

        // Generate Code if not provided
        if (!$request->filled('code')) {
            $lastAccount = Account::latest('code')->first();
            $newCode = $lastAccount ? ((int)$lastAccount->code + 1) : 100;
        } else {
            $newCode = $request->input('code');
        }

        // Update Account
        $account->update([
            'code' => $newCode,
            'account_name_id' => $request->input('account_name_id'),
            'account_type_id' => $request->input('account_type_id'),
            'parent_id' => $request->input('parent_id'),
        ]);

        return redirect()->route('show-account')->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $account = Account::find($request->id);
        $account->delete();

        return response()->json(['success' => 'Account deleted successfully.']);
    }
}
