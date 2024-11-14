<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Gender;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\Product;
use App\Models\Province;
use App\Models\Recovery;
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
                return $query->whereHas('loanApplication.user.profile', function ($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('loanApplication.user', function ($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('loanApplication.user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();

        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->loanApplication->transaction->amount ?? 0;
        });

        $totalMale = $result->filter(function ($transaction) {
            return $transaction->loanApplication->user->profile->gender->name === 'Male';
        })->count();

        $totalFemale = $result->filter(function ($transaction) {
            return $transaction->loanApplication->user->profile->gender->name === 'Female';
        })->count();

        return view('admin.reports.disbursement', compact('title', 'result', 'provinces', 'genders', 'request', 'districts', 'totalAmount', 'totalMale', 'totalFemale'));
    }


    public function showOverdueReport()
    {
        $title = 'Overdue Report';
        $provinces = Province::all();
        $genders = Gender::all();
        return view('admin.reports.overdue', compact('title', 'provinces', 'genders'));
    }

    public function getOverdueReport(Request $request)
    {
        $title = 'Overdue Report';
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

        // Fetch overdue installment details with associated loan applications and users
        $result = InstallmentDetail::with(['installment.loanApplication.user.province', 'installment.loanApplication.user.district'])
            ->where('is_paid', 0) // Only get unpaid installments
            ->where('due_date', '<', now()) // Get records with due date in the past
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('due_date', [$startDate, $endDate]);
            })
            ->when($gender_id, function ($query) use ($gender_id) {
                return $query->whereHas('installment.loanApplication.user.profile', function ($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('installment.loanApplication.user', function ($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('installment.loanApplication.user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();

        // Calculate totals for amount, male, and female
        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->amount_due ?? 0;
        });

        $totalMale = $result->filter(function ($transaction) {
            return $transaction->installment->loanApplication->user->profile->gender->name === 'Male';
        })->count();

        $totalFemale = $result->filter(function ($transaction) {
            return $transaction->installment->loanApplication->user->profile->gender->name === 'Female';
        })->count();

        return view('admin.reports.overdue', compact('title', 'result', 'provinces', 'genders', 'request', 'districts', 'totalAmount', 'totalMale', 'totalFemale'));
    }

    public function showCollectionReport()
    {
        $title = 'Collection Report';
        $provinces = Province::all();
        $genders = Gender::all();
        return view('admin.reports.collection', compact('title', 'provinces', 'genders'));
    }

    public function getCollectionReport(Request $request)
    {
        $title = 'Collection Report';
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

        // Fetch overdue installment details with associated loan applications and users
        $result = Recovery::with(['installmentDetail', 'installment.loanApplication.user.province', 'installment.loanApplication.user.district'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($gender_id, function ($query) use ($gender_id) {
                return $query->whereHas('installment.loanApplication.user.profile', function ($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('installment.loanApplication.user', function ($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('installment.loanApplication.user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();

        // Calculate totals for amount, male, and female
        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->amount ?? 0;
        });

        $totalMale = $result->filter(function ($transaction) {
            return $transaction->installment->loanApplication->user->profile->gender->name === 'Male';
        })->count();

        $totalFemale = $result->filter(function ($transaction) {
            return $transaction->installment->loanApplication->user->profile->gender->name === 'Female';
        })->count();

        return view('admin.reports.collection', compact('title', 'result', 'provinces', 'genders', 'request', 'districts', 'totalAmount', 'totalMale', 'totalFemale'));
    }

    public function showProfitReport()
    {
        $title = 'Profit Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.profit', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getProfitReport(Request $request)
    {
        $title = 'Profit Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRangeSelector = $request->dateRangeSelector;
        $gender_id = $request->gender_id;
        $province_id = $request->province_id;
        $district_id = $request->district_id;
        $product_id = $request->product_id;
        $dateRange = $request->date_range;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null; // Default to null if not set
        $endDate = $splitDate[1] ?? null; // Default to null if not set

        // Fetch overdue installment details with associated loan applications and users
        $result = LoanApplication::with(['product', 'user.province', 'user.district', 'getLatestInstallment'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($product_id, function ($query) use ($product_id) {
                $query->where('product_id', $product_id);
            })
            ->when($gender_id, function ($query) use ($gender_id) {
                return $query->whereHas('user.profile', function ($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('user', function ($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();


        // Calculate totals for amount, male, and female
        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->loan_amount ?? 0;
        });
        $totalPayableAmount = $result->sum(function ($transaction) {
            return $transaction->getLatestInstallment->total_amount ?? 0;
        });
        $totalServiceCharges = $result->sum(function ($transaction) {
            return $transaction->getLatestInstallment->processing_fee ?? 0;
        });
        $totalProfit = $result->sum(function ($transaction) {
            return $transaction->getLatestInstallment->total_markup ?? 0;
        });
        $totalMonthlyInstallment = $result->sum(function ($transaction) {
            return $transaction->getLatestInstallment->monthly_installment ?? 0;
        });


        return view('admin.reports.profit', compact('title', 'result', 'provinces', 'genders',
            'request', 'districts', 'totalAmount', 'totalPayableAmount', 'totalServiceCharges', 'totalProfit', 'totalMonthlyInstallment', 'products'));
    }


    public function showOutstandingReport()
    {
        $title = 'Outstanding Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.outstanding', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getOutstandingReport(Request $request)
    {
        $title = 'Outstanding Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRange = $request->date_range;
        $gender_id = $request->gender_id;
        $province_id = $request->province_id;
        $district_id = $request->district_id;
        $product_id = $request->product_id;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null;
        $endDate = $splitDate[1] ?? null;

        $result = LoanApplication::with([
            'product',
            'user.profile',
            'user.province',
            'user.district',
            'getLatestInstallment' => function ($query) {
                $query->with('details'); // Load installment details for outstanding calculation
            }
        ])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($gender_id, function ($query) use ($gender_id) {
                return $query->whereHas('user.profile', function ($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('user', function ($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();

        // Process each loan application to retrieve required details
        $outstandingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $loanApplicationID = $loan->id;
            $latestInstallment = $loan->getLatestInstallment;

            // Calculate outstanding amount based on unpaid installments
            $outstandingAmount = $latestInstallment->details
                ->where('is_paid', false)
                ->sum('amount_due');

            // Retrieve last payment and next due
            $lastPayment = $latestInstallment->details
                ->where('is_paid', true)
                ->sortByDesc('paid_at')
                ->first();

            $nextDue = $latestInstallment->details
                ->where('is_paid', false)
                ->sortBy('due_date')
                ->first();

            // Determine status (Current if due date is not passed)
            $status = optional($nextDue)->due_date >= now() ? 'Current' : 'Overdue';

            return [
                'id' => $loanApplicationID,
                'installment_id' => $latestInstallment->id,
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_amount' => $outstandingAmount,
                'interest_accrued' => $latestInstallment->total_markup ?? 0,
                'last_payment' => optional($lastPayment)->paid_at,
                'next_due' => optional($nextDue)->due_date,
                'status' => $status,
            ];
        });

        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $outstandingData->sum('outstanding_amount');
        $totalInterestAccrued = $result->sum(fn($loan) => $loan->getLatestInstallment->total_markup ?? 0);


        return view('admin.reports.outstanding', compact(
            'title', 'outstandingData', 'provinces', 'genders',
            'request', 'districts', 'products', 'totalAmount',
            'totalOutstanding', 'totalInterestAccrued'
        ));
    }


    public function showAgingReceivableReport()
    {
        $title = 'Outstanding Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.aging_receivable', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getAgingReceivableReport(Request $request)
    {
        $title = 'Aging Receivable Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRange = $request->date_range;
        $gender_id = $request->gender_id;
        $province_id = $request->province_id;
        $district_id = $request->district_id;
        $product_id = $request->product_id;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null;
        $endDate = $splitDate[1] ?? null;

        $result = LoanApplication::with([
            'product',
            'user.profile',
            'user.province',
            'user.district',
            'getLatestInstallment' => function ($query) {
                $query->with('details'); // Load installment details for outstanding calculation
            }
        ])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($gender_id, function ($query) use ($gender_id) {
                return $query->whereHas('user.profile', function ($q) use ($gender_id) {
                    $q->where('gender_id', $gender_id);
                });
            })
            ->when($province_id, function ($query) use ($province_id) {
                return $query->whereHas('user', function ($q) use ($province_id) {
                    $q->where('province_id', $province_id);
                });
            })
            ->when($district_id, function ($query) use ($district_id) {
                return $query->whereHas('user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            })
            ->get();

        // Process each loan application to retrieve required details
        $agingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $latestInstallment = $loan->getLatestInstallment;

            // Calculate outstanding amount based on unpaid installments
            $outstandingAmount = $latestInstallment->details
                ->where('is_paid', false)
                ->sum('amount_due');

            // Retrieve next due date for aging calculation
            $nextDue = $latestInstallment->details
                ->where('is_paid', false)
                ->sortBy('due_date')
                ->first();

            $daysPastDue = $nextDue ? now()->diffInDays($nextDue->due_date, false) : null; // Calculate days past due
            $status = $this->getStatusFromDaysPastDue($daysPastDue); // Determine status based on days past due

            return [
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_balance' => $outstandingAmount,
                'due_date' => optional($nextDue)->due_date,
                'days_past_due' => $daysPastDue,
                'status' => $status,
            ];
        });

        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $agingData->sum('outstanding_balance');

        return view('admin.reports.aging', compact(
            'title', 'agingData', 'provinces', 'genders',
            'request', 'districts', 'products', 'totalAmount',
            'totalOutstanding'
        ));
    }

    /**
     * Determine status based on days past due.
     *
     * @param int|null $daysPastDue
     * @return string
     */
    private function getStatusFromDaysPastDue($daysPastDue)
    {
        if (is_null($daysPastDue) || $daysPastDue <= 0) {
            return 'Current';
        } elseif ($daysPastDue <= 30) {
            return 'Watchlist';
        } elseif ($daysPastDue <= 60) {
            return 'OAEM';
        } elseif ($daysPastDue <= 90) {
            return 'Substandard';
        } elseif ($daysPastDue <= 179) {
            return 'Doubtful';
        } elseif ($daysPastDue <= 209) {
            return 'Loss';
        } else {
            return 'Write Off';
        }
    }


}
