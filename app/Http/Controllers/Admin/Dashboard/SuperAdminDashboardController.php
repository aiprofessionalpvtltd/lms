<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Allotee;
use App\Models\Bill;
use App\Models\Charge;
use App\Models\Sector;
use App\Models\Size;

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

        $alloteeCount = Allotee::count();
        $sectorCount = Sector::count();
        $chargeCount = Charge::count();
        $sizeCount = Size::count();
        $billCount = Bill::count();
        $paidBillCount = Bill::where('is_paid',1)->count();
        $unpaidBillCount = Bill::where('is_paid',0)->count();
        $arrears = Bill::where('is_paid', '=', 0)
            ->get();
        $totalArrears = $arrears->sum('sub_total');
//        dd($alloteeCount);
        return view('backend.dashboard.super_admin',
            compact(
                'alloteeCount',
                'sectorCount',
                'chargeCount',
                'sizeCount',
                'billCount',
                'paidBillCount',
                'unpaidBillCount',
                'totalArrears'
            ));
    }


}
