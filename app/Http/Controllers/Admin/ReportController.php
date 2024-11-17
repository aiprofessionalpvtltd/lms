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

            $interestAccrued = $outstandingAmount * 0.30 * 30 / 365;
            return [
                'id' => $loanApplicationID,
                'installment_id' => $latestInstallment->id,
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_amount' => $outstandingAmount,
                'interest_accrued' => $interestAccrued,
                'last_payment' => optional($lastPayment)->paid_at,
                'next_due' => optional($nextDue)->due_date,
                'status' => $status,
            ];
        });

        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $outstandingData->sum('outstanding_amount');
        $totalInterestAccrued =$outstandingData->sum('interest_accrued');


        return view('admin.reports.outstanding', compact(
            'title', 'outstandingData', 'provinces', 'genders',
            'request', 'districts', 'products', 'totalAmount',
            'totalOutstanding', 'totalInterestAccrued'
        ));
    }


    public function showAgingReceivableReport()
    {
        $title = 'Aging ReceivableR Report';
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

            // Calculate days past due: positive for upcoming due dates, negative for overdue
            $daysPastDue = $nextDue ? now()->diffInDays($nextDue->due_date, false) : null;

            // Round and format days past due with a sign
            $daysPastDue = $daysPastDue ? sprintf('%+d', round($daysPastDue)) : null;

            $statusData = $this->getStatusFromDaysPastDue(abs((int) $daysPastDue));
            $status = $statusData['status'];
            $percentage = $statusData['percentage'];
            return [
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'id' => $loan->id,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_amount' => $outstandingAmount,
                'due_date' => optional($nextDue)->due_date,
                'provision_amount' => optional($nextDue)->amount_due,
                'days_past_due' => $daysPastDue, // Days past due with sign
                'status' => $status,
                'percentage' => $percentage,
            ];
        });

        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $agingData->sum('outstanding_amount');

         return view('admin.reports.aging_receivable', compact(
            'title', 'agingData', 'provinces', 'genders',
            'request', 'districts', 'products', 'totalAmount',
            'totalOutstanding'
        ));
    }



    public function showProvisionReport()
    {
        $title = 'Provision Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.provision', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getProvisionReport(Request $request)
    {
        $title = 'Provision Report';
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

            // Calculate days past due: positive for upcoming due dates, negative for overdue
            $daysPastDue = $nextDue ? now()->diffInDays($nextDue->due_date, false) : null;

            // Round and format days past due with a sign
            $daysPastDue = $daysPastDue ? sprintf('%+d', round($daysPastDue)) : null;

            $statusData = $this->getStatusFromDaysPastDue(abs((int) $daysPastDue));
            $status = $statusData['status'];
            $percentage = $statusData['percentage'];

            // Determine if the loan is NPL or Not NPL
            $isNPL = in_array($status, ['OAEM', 'Substandard', 'Doubtful', 'Loss']);
            $nplStatus = $isNPL ? 'NPL' : 'Not NPL';

            // Calculate the NPL entry date for Not NPL loans
            $nplEntryDate = null;



            if ($isNPL && $nextDue) {
                $daysUntilNPL = 61 - abs($daysPastDue); // Days to OAEM (NPL entry)
                $nplEntryDate = now()->addDays($daysUntilNPL)->toDateString();
            }


            return [
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'id' => $loan->id,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_amount' => $outstandingAmount,
                'due_date' => optional($nextDue)->due_date,
                'provision_amount' => optional($nextDue)->amount_due,
                'days_past_due' => $daysPastDue, // Days past due with sign
                'status' => $status,
                'percentage' => $percentage,
                'npl_status' => $nplStatus,
                'npl_entry_date' => $nplEntryDate,
            ];
        });


        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $agingData->sum('outstanding_amount');

        return view('admin.reports.provision', compact(
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
            return ['status' => 'Current', 'percentage' => '0%'];
        } elseif ($daysPastDue <= 30) {
            return ['status' => 'Watchlist', 'percentage' => '10%'];
        } elseif ($daysPastDue <= 60) {
            return ['status' => 'OAEM', 'percentage' => '30%'];
        } elseif ($daysPastDue <= 90) {
            return ['status' => 'Substandard', 'percentage' => '50%'];
        } elseif ($daysPastDue <= 179) {
            return ['status' => 'Doubtful', 'percentage' => '70%'];
        } elseif ($daysPastDue <= 209) {
            return ['status' => 'Loss', 'percentage' => '80%'];
        } elseif ($daysPastDue > 210) {
            return ['status' => 'Write Off', 'percentage' => '100%'];
        } else {
            return ['status' => 'Regular', 'percentage' => '0%'];
        }
    }


    public function showFinanceReport()
    {
        $title = 'Product Financing Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.finance', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getFinanceReport(Request $request)
    {
        $title = 'Product Financing Report';
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
            ->when($product_id, function ($query) use ($product_id) {
                return $query->where('product_id', $product_id);
            })
            ->get();

        // Process each loan application
        $agingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $latestInstallment = $loan->getLatestInstallment;
            $installments = $latestInstallment->details;
            $loanProducts = $loan->calculatedProduct;

            // Calculate outstanding amount
            $outstandingAmount = $installments->where('is_paid', false)->sum('amount_due');

            // Retrieve next due date and installment details
            $nextDue = $installments->where('is_paid', false)->sortBy('due_date')->first();

            // Calculate remaining installments
            $remainingInstallments = $installments->where('is_paid', false)->count();
            $paidInstallments = $installments->where('is_paid', true)->count();

            // Calculate installment amount
            $installmentAmount = $installments->first()->amount_due ?? 0;

            $firstInstallmentStarted = $installments->sortBy('due_date')->first();



            return [
                'id' => $loan->id,
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'product' => $loan->product->name ?? 'Standard Loan',
                'product_price' => $loan->product->price ??  $loanProducts->loan_amount,
                'down_payment' => $loanProducts->down_payment_amount,
                'finance_amount' => $loanProducts->financed_amount,
                'loan_start_date' =>  optional($firstInstallmentStarted)->created_at,
                'installment_amount' => $installmentAmount,
                'interest_rate' => $loanProducts->total_interest_amount . '('.  round($loanProducts->interest_rate_percentage) .'%)',
                'installment_due_date' => optional($nextDue)->due_date,
                'installment_paid' => $paidInstallments,
                'remaining_installments' => $remainingInstallments,
                'outstanding_amount' => $outstandingAmount,

            ];
        });

        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $agingData->sum('outstanding_amount');

        return view('admin.reports.finance', compact(
            'title', 'agingData', 'provinces', 'genders',
            'request', 'districts', 'products', 'totalAmount',
            'totalOutstanding'
        ));
    }



}
