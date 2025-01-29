<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;

use App\Models\InstallmentDetail;


class SuperAdminDashboardController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $upcomingInstalments = $this->upcomingInstallments();
//        dd($upcomingInstalment);
        return view('home',compact('upcomingInstalments'));
    }

    public function upcomingInstallments()
    {
        // Get the current date
        $today = now();

        // Get the date 7 days from now
        $nextWeek = now()->addDays(7);

        // Retrieve users with upcoming installments within the next 7 days
        $installments = InstallmentDetail::whereBetween('due_date', [$today, $nextWeek])
            ->where('is_paid', 0) // Only unpaid installments
            ->whereHas('installment.loanApplication', function ($query) {
                $query->where('is_completed', 0); // Filter loan applications where is_completed == 0
            })
            ->with([
                'installment.loanApplication',
                'installment.user.profile', // Load user and profile details
                'installment.user' // Load user details
            ])
            ->orderBy('due_date', 'asc') // Sort by nearest due date
            ->get();

        // Transform data to include required details
        $upcomingInstallments = $installments->map(function ($installment) {
            return [
                'user_name' => optional($installment->installment->user->profile)->first_name . ' ' . optional($installment->installment->user->profile)->last_name, // User Profile Name
                'cnic' => optional($installment->installment->user->profile)->cnic_no, // User CNIC
                'mobile_no' => optional($installment->installment->user->profile)->mobile_no, // User Mobile Number
                'loan_id' => optional($installment->installment->loanApplication)->application_id, // Loan Application ID
                'installment_amount' => $installment->amount_due, // Installment Amount
                'next_due_date' => $installment->due_date, // Next Due Date
            ];
        });

        // Log Activity
        LogActivity::addToLog('Viewed upcoming installments for the next 7 days');

         return $upcomingInstallments;
    }


}
