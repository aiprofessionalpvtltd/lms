<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Gender;
use App\Models\Province;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function showDisbursementReport()
    {
        $title = 'Disbursement Report';
        $provinces = Province::all();
        $genders = Gender::all();
        return view('admin.reports.disbursement', compact('title', 'provinces', 'genders'));
    }

    public function getDisbursementReport(Request $request)
    {
        $title = 'Disbursement Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();


        $dateRangeSelector = $request->dateRangeSelector;
        $gender_id = $request->gender_id;
        $province_id = $request->province_id;
        $district_id = $request->district_id;
        $dateRange = $request->date_range;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null; // Default to null if not set
        $endDate = $splitDate[1] ?? null; // Default to null if not set

        // Fetch transactions with associated loan applications and users
        $result = Transaction::with(['loanApplication.user.province', 'loanApplication.user.district'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($gender_id, function ($query) use ($gender_id) {
                return $query->whereHas('loanApplication.user.profile', function($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('loanApplication.user', function($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('loanApplication.user', function($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();

        $totalAmount = $result->sum(function($transaction) {
            return $transaction->loanApplication->transaction->amount ?? 0;
        });

        $totalMale = $result->filter(function($transaction) {
            return $transaction->loanApplication->user->profile->gender->name === 'Male';
        })->count();

        $totalFemale = $result->filter(function($transaction) {
            return $transaction->loanApplication->user->profile->gender->name === 'Female';
        })->count();

        return view('admin.reports.disbursement', compact('title', 'result' ,'provinces', 'genders' ,'request' ,'districts' ,'totalAmount', 'totalMale', 'totalFemale'));
    }

}
