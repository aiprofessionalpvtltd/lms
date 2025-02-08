<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountNameController extends Controller
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
        $title = 'Account Names';
        $names = AccountName::all();
        return view('admin.account-name.index', compact('title', 'names'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Account Name';
        return view('admin.account-name.create', compact('title'));
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
            'name' => 'required|unique:account_names,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $name = AccountName::create([
            'name' => $request->input('name'),
        ]);

        if ($name) {
            return redirect()->route('show-account-name')->with('success', 'Account Name created successfully.');
        } else {
            return redirect()->route('show-account-name')->with('error', 'Something went wrong.');
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
        $title = 'Account Name Details';
        $name = AccountName::find($id);
        return view('admin.account-name.show', compact('title', 'name'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Account Name';
        $name = AccountName::find($id);
        return view('admin.account-name.edit', compact('title', 'name'));
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
            'name' => 'required|unique:account_names,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $name = AccountName::find($id);
        $name->name = $request->input('name');
        $name->save();

        if ($name) {
            return redirect()->route('show-account-name')->with('success', 'Account Name updated successfully.');
        } else {
            return redirect()->route('show-account-name')->with('error', 'Something went wrong.');
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
        $name = AccountName::find($request->id);
        $name->delete();

        return response()->json(['success' => 'Account Name deleted successfully.']);
    }
}
