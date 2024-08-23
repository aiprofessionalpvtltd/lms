<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-customer'])->only(['index', 'show']);
        $this->middleware(['permission:edit-customer'])->only(['edit', 'update', 'resetID', 'changePassword', 'change']);
        $this->middleware(['permission:create-customer'])->only(['create', 'store']);
        $this->middleware(['permission:delete-customer'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $data = array();
        $title = 'All Customers';
        $customers = User::with('roles', 'profile')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Customer');
            })
            ->orderBy('created_at', 'DESC')
            ->get();
//        dd($customers);
        return view('admin.customer.index', compact('title', 'customers', 'data'));
    }

    public function view($id)
    {
        $title = 'Edit User';
        $customer = User::with('roles', 'profile')->find($id);
        return view('admin.customer.view', compact('title', 'customer'));
    }


}
