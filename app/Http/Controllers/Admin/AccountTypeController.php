<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountTypeController extends Controller
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
        $title = 'Account Types';
        $types = AccountType::all();
        return view('admin.account-type.index', compact('title', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Account Type';
        return view('admin.account-type.create', compact('title'));
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
            'name' => 'required|unique:account_types,name',
            'credit_debit' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $type = AccountType::create([
            'name' => $request->input('name'),
            'is_debit' => $request->input('credit_debit') === 'debit',
            'is_credit' => $request->input('credit_debit') === 'credit',
        ]);


        if ($type) {
            return redirect()->route('show-account-type')->with('success', 'Account Type created successfully.');
        } else {
            return redirect()->route('show-account-type')->with('error', 'Something went wrong.');
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
        $title = 'Account Type Details';
        $type = AccountType::find($id);
        return view('admin.account-type.show', compact('title', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Account Type';
        $type = AccountType::find($id);
        return view('admin.account-type.edit', compact('title', 'type'));
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
            'name' => 'required|unique:account_types,name,' . $id,
            'credit_debit' => 'required',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $type = AccountType::find($id);
        $type->name = $request->input('name');
        $type->is_debit = $request->input('credit_debit') === 'debit';
        $type->is_credit = $request->input('credit_debit') === 'credit';
        $type->save();

        if ($type) {
            return redirect()->route('show-account-type')->with('success', 'Account Type updated successfully.');
        } else {
            return redirect()->route('show-account-type')->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $type = AccountType::find($request->id);
        $type->delete();

        return response()->json(['success' => 'Account Type deleted successfully.']);
    }
}
