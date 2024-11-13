<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;


class CustomerController extends BaseController
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $title = 'All Customers';

        if ($request->ajax()) {
            $customers = User::with(['roles', 'profile', 'tracking'])
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Customer');
                })
                ->orderBy('created_at', 'DESC');

            return DataTables::of($customers)
                ->addColumn('phone_no', function ($customer) {
                    return $customer->profile->mobile_no ?? '';
                })
                ->addColumn('gender', function ($customer) {
                    return $customer->profile->gender->name ?? '';
                })
                ->addColumn('cnic', function ($customer) {
                    return $customer->profile->cnic_no ?? '';
                })
                ->addColumn('province', function ($customer) {
                    return $customer->province->name ?? '';
                })
                ->addColumn('district', function ($customer) {
                    return $customer->district->name ?? '';
                })
                ->addColumn('city', function ($customer) {
                    return $customer->city->name ?? '';
                })
                ->addColumn('score_level', function ($customer) {
                    return $customer->tracking->score ?? 0;
                })
                ->addColumn('risk_assessment', function ($customer) {
                    $score = $customer->tracking->score ?? 0;
                    $riskAssessment = $this->determineRiskLevel($score);
                    return '<span title="' . $riskAssessment['loan_eligibility'] . '">' . $riskAssessment['risk_level'] . '</span>';
                })
                ->addColumn('actions', function ($customer) {
                    $actions = '';
                    if (auth()->user()->can('view-customer')) {
                        $actions .= '<a title="View" href="' . route('view-customer', $customer->id) . '" class="text-primary mr-1"><i class="fas fa-eye"></i></a>';
                    }
                    return '<div class="d-flex">' . $actions . '</div>';
                })
                ->rawColumns(['risk_assessment', 'actions'])
                ->make(true);
        }

        return view('admin.customer.index', compact('title'));
    }

    public function view($id)
    {
        $title = 'Edit User';
        $customer = User::with('roles', 'profile', 'bank_account', 'tracking',
            'employment.employmentStatus', 'employment.incomeSource', 'employment.existingLoan',
            'familyDependent', 'education.education', 'references.relationship')
            ->find($id);

//        if ($customer->tracking->score == 0) {
            $this->calculateUserScore($customer);
//        }

        return view('admin.customer.view', compact('title', 'customer'));
    }


}
