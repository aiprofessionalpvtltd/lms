<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\InventoryRequest;
use App\Models\LeaveRequest;
use App\Models\MeetingSchedule;
use App\Models\Project;
use App\Models\VehicleRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (auth()->user()->isSuperAdmin() || auth()->user()->isAccountUser()|| auth()->user()->isWaterUser()|| auth()->user()->isHousingUser()) {
            $redirect = 'our-dashboard';
            return redirect()->route($redirect);
        }

    }

}
