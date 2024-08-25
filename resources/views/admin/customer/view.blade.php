@extends('admin.layouts.app')
@push('style')

@endpush
@section('content')
    <!--**********************************
            Content body start
        ***********************************-->

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4> <span class="font-weight-semibold"></span>{{$title}}
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>


        </div>


    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <div class="card mb-4">
            <div class="card-header">
                <h3>User Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                         <p><strong>Name:</strong> {{ $customer->name }}</p>
                        <p><strong>Email:</strong> {{ $customer->email }}</p>
                        <p><strong>Email Verified At:</strong> {{ $customer->email_verified_at ?? 'Not Verified' }}</p>
                        <p><strong>Created At:</strong> {{ $customer->created_at }}</p>
                        <p><strong>Updated At:</strong> {{ $customer->updated_at }}</p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>Bank Name:</strong> {{ $customer->bank_account->bank_name }}</p>
                        <p><strong>Account Title:</strong> {{ $customer->bank_account->account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $customer->bank_account->account_number }}</p>
                        <p><strong>IBAN:</strong> {{ $customer->bank_account->iban }}</p>
                        <p><strong>Swift Code:</strong> {{ $customer->bank_account->swift_code }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Profile Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>CNIC No:</strong> {{ $customer->profile->cnic_no }}</p>
                        <p><strong>Issue Date:</strong> {{ $customer->profile->issue_date }}</p>
                        <p><strong>Expire Date:</strong> {{ $customer->profile->expire_date }}</p>
                        <p><strong>Date of Birth:</strong> {{ $customer->profile->dob }}</p>
                        <p><strong>Mobile No:</strong> {{ $customer->profile->mobile_no }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Profile Photo:</strong></p>
                        <img src="{{ asset('storage/' . $customer->profile->photo) }}" alt="Profile Photo" class="img-thumbnail" style="max-width: 150px;">
                        <p><strong>CNIC Photo:</strong></p>
                        <img src="{{ asset('storage/' . $customer->profile->cnic) }}" alt="CNIC Photo" class="img-thumbnail" style="max-width: 150px;">
                    </div>
                </div>
            </div>
        </div>




        <a href="{{ route('show-customer') }}" class="btn btn-primary">Back to Customers List</a>

    </div>
    <!-- /content area -->
    <!--**********************************
        Content body end
    ***********************************-->

@endsection

@push('script')




@endpush
