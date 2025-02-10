@extends('admin.layouts.app')

@section('content')
    <!--**********************************
            Content body start
        ***********************************-->

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold"></span>{{$title}}
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>


        </div>


    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <!-- Form validation -->
        <div class="card">

            <!-- Registration form -->
            <form action="{{route('update-account-transaction', $transaction->id)}}" method="post"
                  name="role_registration" class="flex-fill form-validate-jquery">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">
                                        <label class="col-form-label">Account Head</label>
                                        <div class="form-group">
                                            <select name="account_id" class="form-control select2"
                                                    data-placeholder="Select Account">
                                                <option></option>
                                                @foreach($transactions as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ $transaction->account_id == $row->id ? 'selected' : '' }}>{{ $row->code . ' ' . $row->accountName->name . ' ('. $row->accountType->name.')' }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('account_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('account_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-form-label">Date <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="date" class="form-control flatpickr-minimum"
                                                   placeholder="Select Date "
                                                   value="{{old('date' , $transaction->date)}}"/>
                                            @if ($errors->has('date'))
                                                <span class="text-danger">{{ $errors->first('date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label  ">Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number" required class="form-control"
                                                   name="amount"
                                                   value="{{old('amount',$transaction->amount)}}"
                                                   placeholder=" amount">

                                            @if ($errors->has('amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="col-form-label">Debit/Credit</label>
                                        <div class="form-group">
                                            <select name="credit_debit" class="form-control select2"
                                                    data-placeholder="Select Debit/Credit">
                                                <option></option>
                                                <option
                                                    {{ $transaction->credit_debit == 'credit' ? 'selected' : '' }} value="credit">
                                                    Credit
                                                </option>
                                                <option
                                                    {{ $transaction->credit_debit == 'debit' ? 'selected' : '' }} value="debit">
                                                    Debit
                                                </option>
                                            </select>
                                            @if ($errors->has('credit_debit'))
                                                <span class="text-danger">{{ $errors->first('credit_debit') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <label class="col-form-label">Reference <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="reference" class="form-control"
                                                   placeholder="Enter Reference "
                                                   value="{{old('reference',$transaction->reference)}}"/>
                                            @if ($errors->has('reference'))
                                                <span class="text-danger">{{ $errors->first('reference') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <label class="col-form-label">Transaction Type</label>
                                        <div class="form-group">
                                            <select name="transaction_type" class="form-control select2"
                                                    data-placeholder="Select Transaction Type">
                                                <option></option>

                                                <!-- Income Transactions -->
                                                <optgroup label="Income Transactions">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Loan Disbursement' ? 'selected' : '' }} value="Loan Disbursement">
                                                        Loan Disbursement
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Loan Repayment Received' ? 'selected' : '' }} value="Loan Repayment Received">
                                                        Loan Repayment Received
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Interest Income' ? 'selected' : '' }} value="Interest Income">
                                                        Interest Income
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Service Charges' ? 'selected' : '' }} value="Service Charges">
                                                        Service Charges
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Rental Income' ? 'selected' : '' }} value="Rental Income">
                                                        Rental Income
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Other Income' ? 'selected' : '' }} value="Other Income">
                                                        Other Income
                                                    </option>
                                                </optgroup>

                                                <!-- Expense Transactions -->
                                                <optgroup label="Expense Transactions">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Supplier Payment' ? 'selected' : '' }} value="Supplier Payment">
                                                        Supplier Payment
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Salaries & Wages' ? 'selected' : '' }} value="Salaries & Wages">
                                                        Salaries & Wages
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Utility Bills' ? 'selected' : '' }} value="Utility Bills">
                                                        Utility Bills
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Office Supplies' ? 'selected' : '' }} value="Office Supplies">
                                                        Office Supplies
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Loan Repayment' ? 'selected' : '' }} value="Loan Repayment">
                                                        Loan Repayment
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Tax Payment' ? 'selected' : '' }} value="Tax Payment">
                                                        Tax Payment
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Depreciation Expense' ? 'selected' : '' }} value="Depreciation Expense">
                                                        Depreciation Expense
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Marketing & Advertising' ? 'selected' : '' }} value="Marketing & Advertising">
                                                        Marketing & Advertising
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Other Expenses' ? 'selected' : '' }} value="Other Expenses">
                                                        Other Expenses
                                                    </option>
                                                </optgroup>

                                                <!-- Asset Transactions -->
                                                <optgroup label="Asset Transactions">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Fixed Asset Purchase' ? 'selected' : '' }} value="Fixed Asset Purchase">
                                                        Fixed Asset Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Investment Purchase' ? 'selected' : '' }} value="Investment Purchase">
                                                        Investment Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Cash Withdrawal' ? 'selected' : '' }} value="Cash Withdrawal">
                                                        Cash Withdrawal
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Bank Deposit' ? 'selected' : '' }} value="Bank Deposit">
                                                        Bank Deposit
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Other Assets' ? 'selected' : '' }} value="Other Assets">
                                                        Other Assets
                                                    </option>
                                                </optgroup>

                                                <!-- Liability Transactions -->
                                                <optgroup label="Liability Transactions">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Accounts Payable Settlement' ? 'selected' : '' }} value="Accounts Payable Settlement">
                                                        Accounts Payable Settlement
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Loan Payable' ? 'selected' : '' }} value="Loan Payable">
                                                        Loan Payable
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Other Liabilities' ? 'selected' : '' }} value="Other Liabilities">
                                                        Other Liabilities
                                                    </option>
                                                </optgroup>

                                                <!-- Equity Transactions -->
                                                <optgroup label="Equity Transactions">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Owner’s Capital Contribution' ? 'selected' : '' }} value="Owner’s Capital Contribution">
                                                        Owner’s Capital Contribution
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Equity Withdrawal' ? 'selected' : '' }} value="Equity Withdrawal">
                                                        Equity Withdrawal
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Dividend Payment' ? 'selected' : '' }} value="Dividend Payment">
                                                        Dividend Payment
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Other Equity' ? 'selected' : '' }} value="Other Equity">
                                                        Other Equity
                                                    </option>
                                                </optgroup>

                                                <!-- Office & Equipment Purchases -->
                                                <optgroup label="Office & Equipment Purchases">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Office Rent Payment' ? 'selected' : '' }} value="Office Rent Payment">
                                                        Office Rent Payment
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Office Supplies Purchase' ? 'selected' : '' }} value="Office Supplies Purchase">
                                                        Office Supplies Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Office Furniture Purchase' ? 'selected' : '' }} value="Office Furniture Purchase">
                                                        Office Furniture Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Office Equipment Purchase' ? 'selected' : '' }} value="Office Equipment Purchase">
                                                        Office Equipment Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Computer & Laptop Purchase' ? 'selected' : '' }} value="Computer & Laptop Purchase">
                                                        Computer & Laptop Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Software Purchase & Licensing' ? 'selected' : '' }} value="Software Purchase & Licensing">
                                                        Software Purchase & Licensing
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'IT Hardware Purchase' ? 'selected' : '' }} value="IT Hardware Purchase">
                                                        IT Hardware Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Website Development & Hosting' ? 'selected' : '' }} value="Website Development & Hosting">
                                                        Website Development & Hosting
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Office Renovation & Repairs' ? 'selected' : '' }} value="Office Renovation & Repairs">
                                                        Office Renovation & Repairs
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Furniture Repairs & Maintenance' ? 'selected' : '' }} value="Furniture Repairs & Maintenance">
                                                        Furniture Repairs & Maintenance
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'IT Equipment Repairs' ? 'selected' : '' }} value="IT Equipment Repairs">
                                                        IT Equipment Repairs
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Vehicle Purchase' ? 'selected' : '' }} value="Vehicle Purchase">
                                                        Vehicle Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Fuel & Transportation Expense' ? 'selected' : '' }} value="Fuel & Transportation Expense">
                                                        Fuel & Transportation Expense
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Vehicle Maintenance & Repairs' ? 'selected' : '' }} value="Vehicle Maintenance & Repairs">
                                                        Vehicle Maintenance & Repairs
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Security Equipment Purchase' ? 'selected' : '' }} value="Security Equipment Purchase">
                                                        Security Equipment Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Cleaning Supplies Purchase' ? 'selected' : '' }} value="Cleaning Supplies Purchase">
                                                        Cleaning Supplies Purchase
                                                    </option>
                                                    <option
                                                        {{ $transaction->transaction_type == 'Canteen & Refreshments' ? 'selected' : '' }} value="Canteen & Refreshments">
                                                        Canteen & Refreshments
                                                    </option>
                                                </optgroup>

                                                <!-- Other -->
                                                <optgroup label="Other">
                                                    <option
                                                        {{ $transaction->transaction_type == 'Other' ? 'selected' : '' }} value="Other">
                                                        Other
                                                    </option>
                                                </optgroup>
                                            </select>
                                            @if ($errors->has('transaction_type'))
                                                <span
                                                    class="text-danger">{{ $errors->first('transaction_type') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <label class="col-form-label">Description</label>
                                        <div class="form-group">
                                            <textarea name="description"
                                                      class="form-control">{{old('description',$transaction->description)}}</textarea>

                                            @if ($errors->has('description'))
                                                <span
                                                    class="text-danger">{{ $errors->first('description') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="row">


                                    <div class=" col-md-12 mt-5 ">
                                        <button type="submit"
                                                class="btn btn-outline-primary float-end">
                                            <b><i class="icon-plus3"></i></b> Update
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /registration form -->
        </div>
        <!-- /form validation -->

    </div>
    <!-- /content area -->
    <!--**********************************
        Content body end
    ***********************************-->

@endsection

@push('script')
    <script>
        $(document).ready(function () {

            // Initialize Select2
            $('.select2').select2();
            // Flatpickr
            flatpickr(".flatpickr-minimum");
        });
    </script>

@endpush
