<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerNocController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-customer-noc'])->only(['index', 'show']);
        $this->middleware(['permission:create-customer-noc'])->only(['create', 'store']);
    }


    public function getAllData(Request $request)
    {

        try {

            $loanApplications = LoanApplication::query()
                ->where('is_completed','=',1)
                ->get();


            LogActivity::addToLog('NOC Loan Applications Listing Viewed');

            return view('admin.noc.index', compact('loanApplications'));


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
    }

    public function noc($id)
    {
        $title = 'Customer NOC';
        $loanApplicationID = $id;

//        dd($loanApplicationID);
        try {
            // Fetch loan applications based on the status
            $loanApplication = LoanApplication::find($loanApplicationID);

            // Check if any loan applications are found
            if ($loanApplication == null) {
                return $this->sendError('No Loan Applications found');
            }

            // Retrieve the user's existing or previous loan applications
            $userId = $loanApplication->user_id;
            $customer = User::with('roles', 'profile')
                ->find($userId);



        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }

        return view('admin.customer.noc', compact('title','customer', 'loanApplication'));
    }

}
