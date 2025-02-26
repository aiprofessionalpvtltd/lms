<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\FailedLoginAttempt;
use App\Models\LogActivityView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;


class AdminController extends Controller
{


    public function updatePassword()
    {
        $userID = Auth::user()->id;
        return view('auth.updatePassword')->with(['title' => 'Change Password', 'userID' => $userID]);
    }

    public function ChangePassword(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'oldpassword' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('update-password')
                ->withErrors($validator)
                ->withInput();
        }

        $hashedPassword = Auth::user()->password;

        if (Hash::check($request->oldpassword, $hashedPassword)) {

            if (!Hash::check($request->password, $hashedPassword)) {

                $users = User::find(Auth::user()->id);
                $users->password = $request->password;
                $user = User::where('id', Auth::user()->id)->update(array('password' => $users->password));
                return redirect()->back()->with('success', "Password Change successfully");
            } else {
                return redirect()->back()->with('error', "New password can not be the old password!");
            }
        } else {
            return redirect()->back()->with('error', "Old password doesnt matched");
        }
    }

    public function failedLogs(Request $request)
    {
        $title = 'Failed Login Attempt';

        if ($request->ajax()) {
            $logs = FailedLoginAttempt::orderBy('created_at', 'DESC')->get();

            return DataTables::of($logs)
                ->addColumn('mobile_no', function ($log) {
                    return $log->mobile_no;
                })->addColumn('ip_address', function ($log) {
                    return $log->ip_address;
                })->addColumn('attempted_at', function ($log) {
                    return showDateTime($log->attempted_at);
                })
                ->make(true);
        }

        return view('auth.failed_attempt_logs', compact('title'));
    }

    public function logActivityLists(Request $request)
    {
        $title = 'Activity Logs';

        if ($request->ajax()) {
            $logs = LogActivityView::latest(); // Get latest records & paginate

            return DataTables::of($logs)
                ->addColumn('user', function ($log) {
                    return $log->user_name ?? 'Guest';
                })->addColumn('subject', function ($log) {
                    return $log->subject;
                })->addColumn('url', function ($log) {
                    return '<a href="' . $log->url . '" target="_blank">URL</a>';
                })->addColumn('method', function ($log) {
                    return $log->method;
                })->addColumn('ip', function ($log) {
                    return $log->ip;
                })->addColumn('agent', function ($log) {
                    return $log->agent;
                })->addColumn('created_at', function ($log) {
                    return showDateTime($log->created_at);
                })
                ->rawColumns(['url'])
                ->make(true);
        }

        return view('auth.activity_logs', compact('title'));
    }

}


