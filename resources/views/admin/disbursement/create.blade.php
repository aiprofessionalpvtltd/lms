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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h1>User Information</h1>
                        <p><strong>First Name:</strong> {{ $loanApplication->user->profile->first_name }}</p>
                        <p><strong>Last Name:</strong> {{ $loanApplication->user->profile->last_name }}</p>
                        <p><strong>Gender:</strong> {{ $loanApplication->user->profile->gender->name }}</p>
                        <p><strong>Nationality:</strong> {{ $loanApplication->user->profile->nationality->name }}</p>
                        <p><strong>Province:</strong> {{ $loanApplication->user->province->name }}</p>
                        <p><strong>District:</strong> {{ $loanApplication->user->district->name }}</p>
                        <p><strong>City:</strong> {{ $loanApplication->user->city->name }}</p>
                        <p><strong>Email:</strong> {{ $loanApplication->user->email }}</p>
                    </div>
                    <div class="col-md-4">
                        <h1>Contact Information</h1>
                        <p><strong>CNIC No:</strong> {{ $loanApplication->user->profile->cnic_no }}</p>
                        <p><strong>Issue Date:</strong> {{ showDate($loanApplication->user->profile->issue_date) }}</p>
                        <p><strong>Expire Date:</strong> {{ showDate($loanApplication->user->profile->expire_date) }}
                        </p>
                        <p><strong>Date of Birth:</strong> {{ showDate($loanApplication->user->profile->dob) }}</p>
                        <p><strong>Mobile No:</strong> {{ $loanApplication->user->profile->mobile_no }}</p>
                        <p><strong>Alternate Mobile
                                No:</strong> {{ $loanApplication->user->profile->alternate_mobile_no }}</p>
                        <p><strong>Permanent Address:</strong> {{ $loanApplication->user->profile->permanent_address }}
                        </p>
                        <p><strong>Current Address:</strong> {{ $loanApplication->user->profile->current_address }}</p>
                        <p><strong>Current Address
                                Duration:</strong> {{ $loanApplication->user->profile->residenceDuration->name }}
                        <p><strong>Current
                                Residence:</strong> {{ $loanApplication->user->profile->residenceType->name }}
                        </p>
                    </div>

                    <div class="col-md-4">
                        <h1>Bank Information</h1>
                        <p><strong>Bank Name:</strong> {{ $loanApplication->user->bank_account->bank_name }}</p>
                        <p><strong>Account Title:</strong> {{ $loanApplication->user->bank_account->account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $loanApplication->user->bank_account->account_number }}
                        </p>
                        <p><strong>IBAN:</strong> {{ $loanApplication->user->bank_account->iban }}</p>
                        <p><strong>Swift Code:</strong> {{ $loanApplication->user->bank_account->swift_code }}</p>

                    </div>

                </div>
            </div>


            <!-- Registration form -->
            <form action="{{route('disbursement.store')}}" method="post"
                  name="allotee_registration" class="flex-fill form-validate-jquery">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <input type="hidden" name="loan_application_id" value="{{$id}}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Service/API <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select name="service_api" class="form-control select2" required>
                                                <option value="">Select Service/API</option>
                                                <option value="jazz_cash_mw">Jazz Cash MW (Disburse)</option>
                                                {{--                                                <option value="jazz_cash_ibft">Jazz Cash IBFT (Disburse)</option>--}}
                                                @if($customerBank->code != 1)
                                                    <option value="js_bank_ibft">JS Bank (IBFT)</option>
                                                @else
                                                    <option value="js_bank_ift">JS Bank (IFT)</option>
                                                @endif
                                                <option value="js_bank_coc">JS Bank (COC)</option>
                                                <option value="js_zindagi_wallet">JS Zindagi API Wallet</option>
                                                {{--                                                <option value="tasdeeq_credit_check">Tasdeeq Credit Check</option>--}}
                                                {{--                                                <option value="datacheck">DataCheck</option>--}}
                                                {{--                                                <option value="nacta_aml">NACTA Data (AML)</option>--}}
                                                {{--                                                <option value="js_zindagi_wallet">JS Zindagi API Wallet</option>--}}
                                                {{--                                                <option value="ubl_api">UBL API</option>--}}
                                                {{--                                                <option value="sms_api">SMS API</option>--}}
                                            </select>
                                            <div class="form-control-feedback">
                                                <i class="icon-api text-muted"></i>
                                            </div>
                                            @if ($errors->has('service_api'))
                                                <span class="text-danger">{{ $errors->first('service_api') }}</span>
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


@endpush
