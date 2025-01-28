<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Gender;
use App\Models\InstallmentDetail;
use App\Models\LoanApplication;
use App\Models\Product;
use App\Models\Province;
use App\Models\Recovery;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // Debugging request data
        // dd($request->all());

        $title = 'Disbursement Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();

        $dateRangeSelector = $request->dateRangeSelector;
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
        $district_id = $request->district_id !== 'all' ? $request->district_id : null; // Handle 'all' as null
        $dateRange = $request->date_range;

        // Split the date range
        $splitDate = $dateRange ? str_replace(' ', '', explode('to', $dateRange)) : [null, null];
        $startDate = $splitDate[0] ?? null; // Default to null if not set
        $endDate = $splitDate[1] ?? null; // Default to null if not set

        // Fetch transactions with associated loan applications and users
        $result = Transaction::with(['loanApplication.user.province', 'loanApplication.user.district'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('dateTime', [$startDate, $endDate]);
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
            ->whereHas('loanApplication.user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();


        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->loanApplication->transaction->amount ?? 0;
        });


        $totalMale = 0;
        $totalFemale = 0;

        if ($gender_id === null) { // "all" is selected or gender filter is not applied
            $totalMale = $result->filter(function ($transaction) {
                return optional($transaction->loanApplication?->user?->profile?->gender)->name === 'Male';
            })->count();

            $totalFemale = $result->filter(function ($transaction) {
                return optional($transaction->loanApplication?->user?->profile?->gender)->name === 'Female';
            })->count();
        } else {
            if ($gender_id == 1) { // Assuming 1 represents 'Male'
                $totalMale = $result->filter(function ($transaction) {
                    return optional($transaction->loanApplication?->user?->profile?->gender)->name === 'Male';
                })->count();
            } elseif ($gender_id == 2) { // Assuming 2 represents 'Female'
                $totalFemale = $result->filter(function ($transaction) {
                    return optional($transaction->loanApplication?->user?->profile?->gender)->name === 'Female';
                })->count();
            }
        }

        LogActivity::addToLog('Disbursement report generated');

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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null

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
            ->whereHas('installment.loanApplication.user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Calculate totals for amount, male, and female
        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->amount_due ?? 0;
        });


        $totalMale = 0;
        $totalFemale = 0;

        LogActivity::addToLog('overdue report generated');


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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
        $district_id = $request->district_id;
        $dateRange = $request->date_range;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null; // Default to null if not set
        $endDate = $splitDate[1] ?? null; // Default to null if not set

        // Fetch overdue installment details with associated loan applications and users
        $result = Recovery::with(['installmentDetail', 'installment.loanApplication.user.province', 'installment.loanApplication.user.district'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('recovery_date', [$startDate, $endDate]);
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
            ->whereHas('installment.loanApplication.user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();


        // Calculate totals for amount, male, and female
        $totalAmount = $result->sum(function ($transaction) {
            return $transaction->total_amount ?? 0;
        });

        $totalMale = 0;
        $totalFemale = 0;

        LogActivity::addToLog('collection report generated');

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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
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

        LogActivity::addToLog('profit report generated');


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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
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
            'transaction',
            'getLatestInstallment' => function ($query) {
                $query->with('details'); // Load installment details for outstanding calculation
            }
        ])
            ->where('is_completed', 0) // Add condition for is_completed == 0
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Process each loan application to retrieve required details
        $outstandingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $loanApplicationID = $loan->application_id;
            $latestInstallment = $loan->getLatestInstallment;
            $transaction = $loan->transaction;

            // Initialize default values
            $outstandingAmount = 0;
            $lastPayment = null;
            $nextDue = null;
            $status = 'Unknown';
            $interestAccrued = 0;

            // Check if the latest installment and its details exist
            if ($latestInstallment && $latestInstallment->details) {
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

                // Check if $transaction is not null and has a valid dateTime property
                if ($transaction && $transaction->dateTime) {
                    // Convert the dateTime to a Carbon instance
                    $transactionDate = Carbon::parse($transaction->dateTime);

                    // Calculate the difference in days
                    $noOfDays = number_format($transactionDate->diffInDays(Carbon::now(), false));
                } else {
                    // Handle the case where $transaction is null or doesn't have a valid dateTime
                    $noOfDays = 0; // Or set to a default value or handle the error as needed
                }

                $loanInterestRate = ($loan->product_id != null) ? '0.30' : '0.35';

                $interestAccrued = $outstandingAmount * $loanInterestRate * $noOfDays / 365;
            }
            return [
                'application_id' => $loanApplicationID,
                'installment_id' => $latestInstallment->id ?? 0,
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
        $totalInterestAccrued = $outstandingData->sum('interest_accrued');

        LogActivity::addToLog('outstanding report generated');

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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
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
            ->where('is_completed', 0) // Add condition for is_completed == 0
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Process each loan application to retrieve required details
        $agingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $latestInstallment = $loan->getLatestInstallment;

            // Initialize default values
            $outstandingAmount = 0;
            $nextDue = null;
            $status = 'Unknown';
            $percentage = 0;
            $daysPastDue = 0;

            // Check if the latest installment and its details exist
            if ($latestInstallment && $latestInstallment->details) {
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

                // Skip if daysPastDue is positive (future due date)
                if ($daysPastDue > 0) {
                    return null;
                }

                // Round and format days past due with a sign
                $daysPastDue = $daysPastDue ? sprintf('%+d', round($daysPastDue)) : null;

                $statusData = $this->getStatusFromDaysPastDue(abs((int)$daysPastDue));
                $status = $statusData['status'];
                $percentage = $statusData['percentage'];

            }

            return [
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'application_id' => $loan->application_id,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_amount' => $outstandingAmount,
                'due_date' => optional($nextDue)->due_date,
                'provision_amount' => optional($nextDue)->amount_due,
                'days_past_due' => $daysPastDue, // Days past due with sign
                'status' => $status,
                'percentage' => $percentage,
            ];
        })->filter(); // Remove null entries from skipped loans

        // Calculate totals
        $totalAmount = $agingData->isNotEmpty() ? $agingData->sum('original_loan_amount') : 0;
        $totalOutstanding = $agingData->sum('outstanding_amount');


        LogActivity::addToLog('aging receivable report generated');

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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();


        // Process each loan application to retrieve required details
        $agingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $latestInstallment = $loan->getLatestInstallment;

            // Initialize default values
            $outstandingAmount = 0;
            $nextDue = null;
            $status = 'Unknown';
            $percentage = '0%';
            $daysPastDue = 0;

            // Check if the latest installment and its details exist
            if ($latestInstallment && $latestInstallment->details) {
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

                $statusData = $this->getStatusFromDaysPastDue(abs((int)$daysPastDue));
                $status = $statusData['status'];
                $percentage = $statusData['percentage'];
            }

            // Determine if the loan is NPL or Not NPL
            $isNPL = in_array($status, ['OAEM', 'Substandard', 'Doubtful', 'Loss']);
            $nplStatus = $isNPL ? 'NPL' : 'Not NPL';

            // Calculate the NPL entry date for Not NPL loans
            $nplEntryDate = null;


            if ($isNPL && $nextDue) {
                $daysUntilNPL = 61 - abs($daysPastDue); // Days to OAEM (NPL entry)
                $nplEntryDate = now()->addDays($daysUntilNPL)->toDateString();
            }

//            dd($nextDue);

            return [
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'application_id' => $loan->application_id,
                'original_loan_amount' => $loan->loan_amount,
                'outstanding_amount' => $outstandingAmount,
                'due_date' => optional($nextDue)->due_date ? optional($nextDue)->due_date : '', // Fix applied here
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

        LogActivity::addToLog('provision report generated');

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
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
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
            ->whereNotNull('product_id')
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Process each loan application
        $agingData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $latestInstallment = $loan->getLatestInstallment;
            $installments = $latestInstallment->details;
            $loanProducts = $loan->calculatedProduct;

            // Initialize default values
            $outstandingAmount = 0;
            $nextDue = null;

            $remainingInstallments = 0;
            $paidInstallments = 0;
            $installmentAmount = 0;
            $firstInstallmentStarted = 0;

            // Check if the latest installment and its details exist
            if ($latestInstallment && $latestInstallment->details) {
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

            }

            return [
                'application_id' => $loan->application_id,
                'customer_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'product' => $loan->product->name ?? 'Standard Loan',
                'product_price' => $loan->product->price ?? $loanProducts->loan_amount,
                'down_payment' => $loanProducts->down_payment_amount,
                'finance_amount' => $loanProducts->financed_amount,
                'loan_start_date' => optional($firstInstallmentStarted)->created_at,
                'installment_amount' => $installmentAmount,
                'interest_rate' => $loanProducts->total_interest_amount . '(' . round($loanProducts->interest_rate_percentage) . '%)',
                'installment_due_date' => optional($nextDue)->due_date,
                'installment_paid' => $paidInstallments,
                'remaining_installments' => $remainingInstallments,
                'outstanding_amount' => $outstandingAmount,

            ];
        });

        // Calculate totals
        $totalAmount = $result->sum('loan_amount');
        $totalOutstanding = $agingData->sum('outstanding_amount');

        LogActivity::addToLog('finance report generated');

        return view('admin.reports.finance', compact(
            'title', 'agingData', 'provinces', 'genders',
            'request', 'districts', 'products', 'totalAmount',
            'totalOutstanding'
        ));
    }

    public function showPenaltyReport()
    {
        $title = 'Penalty Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.penalty', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getPenaltyReport(Request $request)
    {
        $title = 'Penalty Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRange = $request->date_range;
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
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
                $query->with('details'); // Load installment details for penalty calculation
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Define penalty per day
        $penaltyPerDay = env('LATE_FEE');

        // Process each loan application
        $penaltyData = $result->map(function ($loan) use ($penaltyPerDay) {
            $penaltyEntries = [];
            $daysLate = 0;
            $totalPenalty = 0;

            // Ensure getLatestInstallment is not null
            $latestInstallment = $loan->getLatestInstallment;

            if ($latestInstallment && $latestInstallment->details) {
                $installments = $latestInstallment->details;

                foreach ($installments as $installment) {
                    // Check if the installment is unpaid and overdue
                    if (!$installment->is_paid && $installment->due_date < now()) {
                        // Add installment number (ordinal formatting)
                        $installments->each(function ($installment, $index) {
                            $installment->installment_number = $this->formatOrdinal($index + 1);
                        });

                        // Calculate days late and penalty
                        $daysLate = abs(now()->diffInDays($installment->due_date));
                        $totalPenalty = $daysLate * $penaltyPerDay;

                        // Add penalty entry
                        $penaltyEntries[] = [
                            'application_id' => $loan->application_id,
                            'borrower_name' => "{$loan->user->profile->first_name} {$loan->user->profile->last_name}",
                            'cnic' => $loan->user->profile->cnic_no,
                            'installment_number' => $installment->installment_number,
                            'installment_amount' => round($installment->amount_due),
                            'installment_due_date' => $installment->due_date,
                            'days_late' => round($daysLate, 0),
                            'penalty_per_day' => round($penaltyPerDay),
                            'total_penalty' => round($totalPenalty),
                            'total_payment' => round($installment->amount_due + $totalPenalty),
                        ];
                    }
                }
            }

            return $penaltyEntries;
        })->flatten(1);

//        dd($penaltyData);
        LogActivity::addToLog('penalty report generated');

        return view('admin.reports.penalty', compact(
            'title', 'penaltyData', 'provinces', 'genders',
            'request', 'districts', 'products'
        ));
    }


    private function formatOrdinal($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        $mod = $number % 100;
        return $number . ($suffixes[($mod - 20) % 10] ?? $suffixes[$mod] ?? 'th');
    }

    public function showPrincipalReport()
    {
        $title = 'Principal Payment Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.principal', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getPrincipalReport(Request $request)
    {
        $title = 'Principal Payment Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRange = $request->date_range;
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
        $district_id = $request->district_id;
        $product_id = $request->product_id;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null;
        $endDate = $splitDate[1] ?? null;

        $result = LoanApplication::with([
            'product',
            'calculatedProduct',
            'user.profile',
            'user.province',
            'user.district',
            'getLatestInstallment' => function ($query) {
                $query->with('details'); // Load installment details
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Process each loan application
        $principalData = $result->map(function ($loan) {
            $principalEntries = [];
            $userProfile = optional($loan->user->profile);
            $installment = $loan->getLatestInstallment;
            $installmentDetail = $loan->calculatedProduct;

            // Ensure $installment and $installmentDetail are not null
            if ($installment && $installmentDetail) {
                // Interest received calculation
                $interestReceived = $installment->monthly_installment / 12;

                // Principal received calculation
                $principalReceived = $installment->monthly_installment - $interestReceived;

                // Remaining principal calculation
                $remainingPrincipal = $loan->loan_amount - $principalReceived;

                // Remaining interest calculation
                $remainingInterest = $installment->total_markup - $interestReceived;

                // Number of installments calculation
                $totalInstallments = $loan->installments->count(); // Total installments
                $paidInstallments = round($principalReceived / $installmentDetail->monthly_installment_amount); // Approximating
                $remainingInstallments = $totalInstallments - $paidInstallments;

                // Add to report data
                $principalEntries[] = [
                    'application_id' => $loan->application_id,
                    'borrower_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                    'cnic' => $userProfile->cnic_no,
                    'loan_amount' => round($loan->loan_amount, 2),
                    'principal' => round($loan->loan_amount, 2),
                    'interest_amount' => round($installment->total_markup, 2) . ' (' . $installmentDetail->interest_rate_percentage . '%)',
                    'principal_plus_interest' => round($loan->loan_amount + $installment->total_markup, 2),
                    'installment_amount' => round($installmentDetail->monthly_installment_amount, 2),
                    'interest_received' => round($interestReceived, 2),
                    'principal_received' => round($principalReceived, 2),
                    'remaining_principal' => round($remainingPrincipal, 2),
                    'remaining_interest' => round($remainingInterest, 2), // Added
                    'total_installments' => $totalInstallments, // Added
                    'paid_installments' => $paidInstallments, // Added
                    'remaining_installments' => $remainingInstallments, // Added
                ];
            }

            return $principalEntries;
        })->flatten(1);


//        dd($principalData);

        LogActivity::addToLog('principal report generated');

        return view('admin.reports.principal', compact(
            'title', 'principalData', 'provinces', 'genders',
            'request', 'districts', 'products'
        ));
    }


    public function showInterestIncomeReport()
    {
        $title = 'Interest Income Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.interest_income', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getInterestIncomeReport(Request $request)
    {
        $title = 'Interest Income Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRange = $request->date_range;
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
        $district_id = $request->district_id;
        $product_id = $request->product_id;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null;
        $endDate = $splitDate[1] ?? null;

        $result = LoanApplication::with([
            'product',
            'calculatedProduct',
            'user.profile',
            'user.province',
            'user.district',
            'installments.details' => function ($query) {
                $query->orderBy('due_date', 'asc'); // Ensure details are ordered by due date
            },
            'transaction' // Assuming this provides the disbursement date
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
            })->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();


        // Process each loan application
        $interestIncomeData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $calculatedProduct = $loan->calculatedProduct;

            // Fetch installments and their details
            $installments = $loan->installments;

            // Get the start and end dates from installment details
            $allDetails = $installments->flatMap->details; // Flatten all details across installments
            $startDate = optional($allDetails->first())->issue_date ?? 'N/A';
            $endDate = optional($allDetails->last())->due_date ?? 'N/A';

            // Extract necessary values
            $loanAmount = $loan->loan_amount;
            $interestRate = ($calculatedProduct->interest_rate_percentage ?? 0) / 100; // Convert percentage to decimal
            $loanDurationMonths = $loan->loanDuration->value ?? 0; // Assuming this is in months

            // Interest income calculation
            $interestIncome = $loanAmount * $interestRate * ($loanDurationMonths / 12);

            return [
                'application_id' => $loan->application_id,
                'borrower_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'loan_amount' => round($loanAmount, 2),
                'interest_rate' => round($calculatedProduct->interest_rate_percentage ?? 0, 2) . '%',
                'interest_income' => round($interestIncome, 2),
                'disbursement_date' => optional($loan->transaction)->dateTime ? showDate($loan->transaction->dateTime) : '',
                'installment_start_date' => $startDate,
                'installment_end_date' => $endDate,
            ];
        });

        LogActivity::addToLog('interest income report generated');

        return view('admin.reports.interest_income', compact(
            'title', 'interestIncomeData', 'provinces', 'genders',
            'request', 'districts', 'products'
        ));
    }

    public function showEarlySettlementReport()
    {
        $title = 'Early Settlement Report';
        $provinces = Province::all();
        $genders = Gender::all();
        $products = Product::all();
        return view('admin.reports.early_settlement', compact('title', 'provinces', 'genders', 'products'));
    }

    public function getEarlySettlementReport(Request $request)
    {
        $title = 'Early Settlement Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
        $products = Product::all();

        $dateRange = $request->date_range;
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
        $district_id = $request->district_id;
        $product_id = $request->product_id;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null;
        $endDate = $splitDate[1] ?? null;

        $result = LoanApplication::with([
            'product',
            'calculatedProduct',
            'user.profile',
            'user.province',
            'user.district',
            'installments.details.recovery',
        ])
            ->whereHas('installments.recoveries', function ($query) {
                $query->where('is_early_settlement', 1);
            })
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();

        // Process each loan application
        $recoveryData = $result->map(function ($loan) {
            $userProfile = $loan->user->profile;
            $installments = $loan->installments;
            $loanAmount = $loan->loan_amount;

            // Filter for early settlement recoveries
            $earlySettlementRecoveries = $installments->flatMap->recoveries->filter(function ($recovery) {
                return $recovery->is_early_settlement === 1;
            });

            // If no early settlement recoveries, skip
            if ($earlySettlementRecoveries->isEmpty()) {
                return null;
            }

            $latestRecovery = $earlySettlementRecoveries->sortByDesc('created_at')->first();
            $settlementChargesPercentage = $latestRecovery ? $latestRecovery->percentage : 0;
            $ercAmount = $latestRecovery ? str_replace(',', '', $latestRecovery->erc_amount) : 0;

            // Remaining loan amount
            $remainingLoanAmount = $latestRecovery ? str_replace(',', '', $latestRecovery->remaining_amount) : 0;

            // Calculate Total Payable
            $totalPayable = $remainingLoanAmount + round($ercAmount, 2);

            return [
                'loan_id' => $loan->application_id,
                'borrower_name' => "{$userProfile->first_name} {$userProfile->last_name}",
                'cnic' => $userProfile->cnic_no,
                'loan_amount' => round($loanAmount, 2),
                'total_installments' => $installments->flatMap->details->count(),
                'installment_paid' => $installments->flatMap->details->filter(function ($detail) {
                    return $detail->recovery && $detail->recovery->amount >= $detail->total_amount;
                })->count(),
                'remaining_loan_amount' => round($remainingLoanAmount, 2),
                'settlement_charges_percentage' => round($settlementChargesPercentage, 2),
                'settlement_charges_pkr' => round($ercAmount, 2),
                'total_payable' => round($totalPayable, 2),
            ];
        })->filter(); // Remove null entries

//        dd($recoveryData);
        LogActivity::addToLog('Early settlement report generated');

        return view('admin.reports.early_settlement', compact(
            'title', 'recoveryData', 'provinces', 'genders',
            'request', 'districts', 'products'
        ));
    }

    public function showInvoiceReport()
    {
        $title = 'Early Settlement Report';
        $customers = User::with(['roles', 'profile'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Customer');
            })
            ->orderBy('created_at', 'DESC')->get();
//        dd($customers);
        return view('admin.reports.invoice', compact('title', 'customers'));
    }

    public function getInvoiceReport(Request $request)
    {
        $title = 'Loan Payment Invoice';
        $customer_id = $request->customer_id;
        $application_id = $request->application_id;
        $customers = User::with(['roles', 'profile'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Customer');
            })
            ->orderBy('created_at', 'DESC')->get();
        $loan = LoanApplication::with([
            'product',
            'calculatedProduct',
            'user.profile',
            'user.province',
            'user.district',
            'installments.details',
            'installments.recoveries',
        ])
            ->when($application_id, function ($query) use ($application_id) {
                $query->where('id', $application_id);
            })
            ->when($customer_id, function ($query) use ($customer_id) {
                $query->where('user_id', $customer_id);
            })
            ->first();

        if (!$loan) {
            return back()->with('error', 'Loan application not found.');
        }

        $userProfile = $loan->user->profile;
        $calculatedProduct = $loan->calculatedProduct;

        $installmentDetails = $loan->installments->flatMap->details;
        $recoveryDetails = $loan->installments->flatMap->recoveries;

        $invoiceData = [
            'loan_id' => $loan->application_id,
            'borrower_name' => "{$userProfile->first_name} {$userProfile->last_name}",
            'cnic' => $userProfile->cnic_no,
            'mobile_no' => $userProfile->mobile_no,
            'loan_amount' => round($loan->loan_amount, 2),
            'loan_account_no' => $loan->application_id,
            'processing_fee_percentage' => $calculatedProduct->processing_fee_percentage,
            'processing_fee' => round($calculatedProduct->processing_fee_amount),
            'total_interest' => round($calculatedProduct->total_interest_amount, 2),
            'total_payable' => round($calculatedProduct->total_repayable_amount, 2),
            'monthly_installment' => round($calculatedProduct->monthly_installment_amount, 2),
            'installments' => $installmentDetails,
            'recoveries' => $recoveryDetails,
        ];

//        dd($invoiceData);
        return view('admin.reports.invoice', compact('title', 'invoiceData', 'customers', 'loan'));
    }

    public function generatePDF(Request $request)
    {
        $title = 'Loan Payment Invoice';
        $customer_id = $request->customer_id;
        $application_id = $request->application_id;

        $loan = LoanApplication::with([
            'product',
            'calculatedProduct',
            'user.profile',
            'user.province',
            'user.district',
            'installments.details',
            'installments.recoveries',

        ])
            ->when($application_id, function ($query) use ($application_id) {
                $query->where('id', $application_id);
            })
            ->when($customer_id, function ($query) use ($customer_id) {
                $query->where('user_id', $customer_id);
            })
            ->first();

        if (!$loan) {
            return back()->with('error', 'Loan application not found.');
        }

        $userProfile = $loan->user->profile;
        $calculatedProduct = $loan->calculatedProduct;

        $installmentDetails = $loan->installments->flatMap->details;
        $recoveryDetails = $loan->installments->flatMap->recoveries;

        $invoiceData = [
            'loan_id' => $loan->application_id,
            'borrower_name' => "{$userProfile->first_name} {$userProfile->last_name}",
            'cnic' => $userProfile->cnic_no,
            'mobile_no' => $userProfile->mobile_no,
            'loan_amount' => round($loan->loan_amount, 2),
            'loan_account_no' => $loan->application_id,
            'processing_fee_percentage' => $calculatedProduct->processing_fee_percentage,
            'processing_fee' => round($calculatedProduct->processing_fee_amount),
            'total_interest' => round($calculatedProduct->total_interest_amount, 2),
            'total_payable' => round($calculatedProduct->total_repayable_amount, 2),
            'monthly_installment' => round($calculatedProduct->monthly_installment_amount, 2),
            'installments' => $installmentDetails,
            'recoveries' => $recoveryDetails,

        ];

        $path = public_path('backend/img/icons/logo.jpg'); // Adjust path as needed
        if (file_exists($path)) {
            $imageData = base64_encode(file_get_contents($path));
            $imageSrc = 'data:image/jpeg;base64,' . $imageData;
        } else {
            $imageSrc = ''; // Default to an empty string or placeholder image
        }


        // Generate PDF
        $pdf = Pdf::loadView('admin.reports.invoice-pdf', compact('title', 'invoiceData', 'imageSrc'))->setPaper('a4', 'portrait');;


        // Return the PDF for download
        return $pdf->download($invoiceData['borrower_name'] . '_Invoice.pdf');
    }


    public function showCompleteReport()
    {
        $title = 'Completed Application Report';

        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();
//        dd($customers);
        return view('admin.reports.complete', compact('title', 'genders', 'provinces'));
    }

    public function getCompleteReport(Request $request)
    {
        $title = 'Completed Application Report';
        $provinces = Province::all();
        $districts = District::all();
        $genders = Gender::all();


        $dateRangeSelector = $request->dateRangeSelector;
        $gender_id = $request->gender_id !== 'all' ? $request->gender_id : null; // Handle 'all' as null
        $province_id = $request->province_id !== 'all' ? $request->province_id : null; // Handle 'all' as null
        $district_id = $request->district_id;
        $dateRange = $request->date_range;

        // Split the date range
        $splitDate = str_replace(' ', '', explode('to', $dateRange));
        $startDate = $splitDate[0] ?? null; // Default to null if not set
        $endDate = $splitDate[1] ?? null; // Default to null if not set

        // Fetch transactions with associated loan applications and users
        $result = LoanApplication::with([
            'product',
            'user.profile',
            'user.province',
            'user.district',
            'getLatestInstallment' => function ($query) {
                $query->with('details'); // Load installment details for outstanding calculation
            }
        ])
            ->where('is_completed', '=', 1)
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
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Customer'); // Assuming the role name is 'Customer'
            })
            ->get();


        LogActivity::addToLog('Completed Application report generated');

        return view('admin.reports.complete', compact('title', 'result', 'provinces', 'genders',
            'request', 'districts'));
    }


}
