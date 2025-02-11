<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountVendor;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-vendors-accounts'])->only(['index', 'show']);
        $this->middleware(['permission:create-vendors-accounts'])->only(['create', 'store']);
        $this->middleware(['permission:edit-vendors-accounts'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-vendors-accounts'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Vendors Accounts';
        $vendorAccounts = AccountVendor::with('province','district','city')->get();
        return view('admin.vendor_account.index', compact('title', 'vendorAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Vendor Account';
        $provinces = Province::all();
        $districts = District::all();
        $cities = City::all();
        return view('admin.vendor_account.create', compact('title', 'provinces','districts','cities'));
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
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:account_vendors,email',
                'phone_no' => 'required|unique:account_vendors,phone',
                'cnic_no' => 'required|unique:account_vendors,cnic_no',
                'business_name' => 'required|string|max:255',
                'bank_name' => 'required|string|max:255',
                'iban_no' => 'required|unique:account_vendors,iban_no',
                'province_id' => 'required|exists:provinces,id',
                'district_id' => 'required|exists:districts,id',
                'city_id' => 'required|exists:cities,id',
                'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vendorAccount = AccountVendor::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone_no'),
            'cnic_no' => $request->input('cnic_no'),
            'business_name' => $request->input('business_name'),
            'bank_name' => $request->input('bank_name'),
            'iban_no' => $request->input('iban_no'),
            'province_id' => $request->input('province_id'),
            'district_id' => $request->input('district_id'),
            'city_id' => $request->input('city_id'),
            'address' => $request->input('address'),
        ]);
        if ($vendorAccount) {
            return redirect()->route('show-vendor-account')->with('success', 'Vendor Account created successfully.');
        } else {
            return redirect()->route('show-vendor-account')->with('error', 'Something went wrong.');
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Vendor Account';
        $vendorAccount = AccountVendor::find($id);
        $provinces = Province::all();
        $districts = District::all();
        $cities = City::all();
        return view('admin.vendor_account.edit', compact('title', 'vendorAccount', 'provinces','districts','cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the vendor account by ID
        $vendorAccount = AccountVendor::findOrFail($id);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:account_vendors,email,' . $id,
            'phone_no' => 'required|unique:account_vendors,phone,' . $id,
            'cnic_no' => 'required|unique:account_vendors,cnic_no,' . $id,
            'business_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban_no' => 'required|unique:account_vendors,iban_no,' . $id,
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update the vendor account
        $updated = $vendorAccount->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone_no'),
            'cnic_no' => $request->input('cnic_no'),
            'business_name' => $request->input('business_name'),
            'bank_name' => $request->input('bank_name'),
            'iban_no' => $request->input('iban_no'),
            'province_id' => $request->input('province_id'),
            'district_id' => $request->input('district_id'),
            'city_id' => $request->input('city_id'),
            'address' => $request->input('address'),
        ]);

        // Check if the update was successful
        if ($updated) {
            return redirect()->route('show-vendor-account')->with('success', 'Vendor Account updated successfully.');
        } else {
            return redirect()->route('show-vendor-account')->with('error', 'Something went wrong while updating the Vendor Account.');
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
        $vendoraccount = AccountVendor::find($request->id);
        $vendoraccount->delete();

        return response()->json(['success' => 'Vendor Account deleted successfully.']);
    }
}
