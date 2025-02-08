@extends('admin.layouts.app')
@push('style')
    <link href="{{asset('backend/vendor/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('backend/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}" rel="stylesheet">

@endpush
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
            <form action="{{route('store-account-transaction')}}" method="post"
                  name="allotee_registration" class="flex-fill form-validate-jquery">
                @csrf

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
                                                @foreach($accounts as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('account_id') == $row->id ? 'selected' : '' }}>{{ $row->code . ' ' . $row->accountName->name . ' ('. $row->accountType->name.')' }}</option>
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
                                                   value="{{old('date' , currentDateInsert())}}"/>
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
                                                   value="{{old('amount')}}"
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
                                                <option  {{ old('credit_debit') == 'credit' ? 'selected' : '' }} value="credit">Credit</option>
                                                <option  {{ old('credit_debit') == 'debit' ? 'selected' : '' }} value="debit">Debit</option>
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
                                                   value="{{old('reference')}}"/>
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
                                                    <option value="Loan Disbursement">Loan Disbursement</option>
                                                    <option value="Loan Repayment Received">Loan Repayment Received
                                                    </option>
                                                    <option value="Interest Income">Interest Income</option>
                                                    <option value="Service Charges">Service Charges</option>
                                                    <option value="Rental Income">Rental Income</option>
                                                    <option value="Other Income">Other Income</option>
                                                </optgroup>

                                                <!-- Expense Transactions -->
                                                <optgroup label="Expense Transactions">
                                                    <option value="Supplier Payment">Supplier Payment</option>
                                                    <option value="Salaries & Wages">Salaries & Wages</option>
                                                    <option value="Utility Bills">Utility Bills</option>
                                                    <option value="Office Supplies">Office Supplies</option>
                                                    <option value="Loan Repayment">Loan Repayment</option>
                                                    <option value="Tax Payment">Tax Payment</option>
                                                    <option value="Depreciation Expense">Depreciation Expense</option>
                                                    <option value="Marketing & Advertising">Marketing & Advertising
                                                    </option>
                                                    <option value="Other Expenses">Other Expenses</option>
                                                </optgroup>

                                                <!-- Asset Transactions -->
                                                <optgroup label="Asset Transactions">
                                                    <option value="Fixed Asset Purchase">Fixed Asset Purchase</option>
                                                    <option value="Investment Purchase">Investment Purchase</option>
                                                    <option value="Cash Withdrawal">Cash Withdrawal</option>
                                                    <option value="Bank Deposit">Bank Deposit</option>
                                                    <option value="Other Assets">Other Assets</option>
                                                </optgroup>

                                                <!-- Liability Transactions -->
                                                <optgroup label="Liability Transactions">
                                                    <option value="Accounts Payable Settlement">Accounts Payable
                                                        Settlement
                                                    </option>
                                                    <option value="Loan Payable">Loan Payable</option>
                                                    <option value="Other Liabilities">Other Liabilities</option>
                                                </optgroup>

                                                <!-- Equity Transactions -->
                                                <optgroup label="Equity Transactions">
                                                    <option value="Owner’s Capital Contribution">Owner’s Capital
                                                        Contribution
                                                    </option>
                                                    <option value="Equity Withdrawal">Equity Withdrawal</option>
                                                    <option value="Dividend Payment">Dividend Payment</option>
                                                    <option value="Other Equity">Other Equity</option>
                                                </optgroup>

                                                <optgroup label="Office & Equipment Purchases">
                                                    <option value="Office Rent Payment">Office Rent Payment</option>
                                                    <option value="Office Supplies Purchase">Office Supplies Purchase
                                                    </option>
                                                    <option value="Office Furniture Purchase">Office Furniture
                                                        Purchase
                                                    </option>
                                                    <option value="Office Equipment Purchase">Office Equipment
                                                        Purchase
                                                    </option>
                                                    <option value="Computer & Laptop Purchase">Computer & Laptop
                                                        Purchase
                                                    </option>
                                                    <option value="Software Purchase & Licensing">Software Purchase &
                                                        Licensing
                                                    </option>
                                                    <option value="IT Hardware Purchase">IT Hardware Purchase</option>
                                                    <option value="Website Development & Hosting">Website Development &
                                                        Hosting
                                                    </option>
                                                    <option value="Office Renovation & Repairs">Office Renovation &
                                                        Repairs
                                                    </option>
                                                    <option value="Furniture Repairs & Maintenance">Furniture Repairs &
                                                        Maintenance
                                                    </option>
                                                    <option value="IT Equipment Repairs">IT Equipment Repairs</option>
                                                    <option value="Vehicle Purchase">Vehicle Purchase</option>
                                                    <option value="Fuel & Transportation Expense">Fuel & Transportation
                                                        Expense
                                                    </option>
                                                    <option value="Vehicle Maintenance & Repairs">Vehicle Maintenance &
                                                        Repairs
                                                    </option>
                                                    <option value="Security Equipment Purchase">Security Equipment
                                                        Purchase
                                                    </option>
                                                    <option value="Cleaning Supplies Purchase">Cleaning Supplies
                                                        Purchase
                                                    </option>
                                                    <option value="Canteen & Refreshments">Canteen & Refreshments
                                                    </option>
                                                </optgroup>


                                                <!-- Other -->
                                                <optgroup label="Other">
                                                    <option value="Other">Other</option>
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
                                                      class="form-control">{{old('description')}}</textarea>

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
                                            <b><i class="icon-plus3"></i></b> Save
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
