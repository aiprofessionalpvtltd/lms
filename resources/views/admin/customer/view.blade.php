@extends('admin.layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

@endpush
@section('content')
    <!--**********************************
            Content body start
        ***********************************-->


    <div class="container">
         <ul class="list-group list-group-horizontal-md text-center">
            <li class="list-group-item flex-fill {{ $customer->tracking->is_registration ? 'bg-success text-white' : 'bg-danger text-white' }}">
                Registration
                @if($customer->tracking->is_registration)
                    <i class="bi bi-check-circle-fill"></i> <!-- Bootstrap icon for success -->
                @else
                    <i class="bi bi-x-circle-fill"></i> <!-- Bootstrap icon for failure -->
                @endif
            </li>
            <li class="list-group-item flex-fill {{ $customer->tracking->is_kyc ? 'bg-success text-white' : 'bg-danger text-white' }}">
                KYC
                @if($customer->tracking->is_kyc)
                    <i class="bi bi-check-circle-fill"></i>
                @else
                    <i class="bi bi-x-circle-fill"></i>
                @endif
            </li>
            <li class="list-group-item flex-fill {{ $customer->tracking->is_profile ? 'bg-success text-white' : 'bg-danger text-white' }}">
                Profile
                @if($customer->tracking->is_profile)
                    <i class="bi bi-check-circle-fill"></i>
                @else
                    <i class="bi bi-x-circle-fill"></i>
                @endif
            </li>
            <li class="list-group-item flex-fill {{ $customer->tracking->is_reference ? 'bg-success text-white' : 'bg-danger text-white' }}">
                Reference
                @if($customer->tracking->is_reference)
                    <i class="bi bi-check-circle-fill"></i>
                @else
                    <i class="bi bi-x-circle-fill"></i>
                @endif
            </li>
            <li class="list-group-item flex-fill {{ $customer->tracking->is_bank_statement ? 'bg-success text-white' : 'bg-danger text-white' }}">
                Bank Statement
                @if($customer->tracking->is_bank_statement)
                    <i class="bi bi-check-circle-fill"></i>
                @else
                    <i class="bi bi-x-circle-fill"></i>
                @endif
            </li>
            <li class="list-group-item flex-fill {{ $customer->tracking->is_address_proof ? 'bg-success text-white' : 'bg-danger text-white' }}">
                Address Verification
                @if($customer->tracking->is_address_proof)
                    <i class="bi bi-check-circle-fill"></i>
                @else
                    <i class="bi bi-x-circle-fill"></i>
                @endif
            </li>
        </ul>
    </div>
    <!-- Content area -->
    <div class="content">


        <div class="card mb-4">

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h1>User Information</h1>
                        <p><strong>First Name:</strong> {{ $customer->profile->first_name }}</p>
                        <p><strong>Last Name:</strong> {{ $customer->profile->last_name }}</p>
                        <p><strong>Gender:</strong> {{ $customer->profile->gender->name }}</p>
                        <p><strong>Nationality:</strong> {{ $customer->profile->nationality->name }}</p>
                        <p><strong>Province:</strong> {{ $customer->province->name }}</p>
                        <p><strong>District:</strong> {{ $customer->district->name }}</p>
                        <p><strong>City:</strong> {{ $customer->city->name }}</p>
                        <p><strong>Email:</strong> {{ $customer->email }}</p>
                        <p><strong>CNIC No:</strong> {{ $customer->profile->cnic_no }}</p>
                        <p><strong>Issue Date:</strong> {{ showDate($customer->profile->issue_date) }}</p>
                        <p><strong>Expire Date:</strong> {{ showDate($customer->profile->expire_date) }}</p>
                        <p><strong>Date of Birth:</strong> {{ showDate($customer->profile->dob) }}</p>
                        <p><strong>Mobile No:</strong> {{ $customer->profile->mobile_no }}</p>
                        <p><strong>Alternate Mobile No:</strong> {{ $customer->profile->alternate_mobile_no }}</p>
                        <p><strong>Permanent Address:</strong> {{ $customer->profile->permanent_address }}</p>
                        <p><strong>Current Address:</strong> {{ $customer->profile->current_address }}</p>
                        <p><strong>Current Address Duration:</strong> {{ $customer->profile->residenceDuration->name }}
                        <p><strong>Current Residence:</strong> {{ $customer->profile->residenceType->name }}
                        </p>
                    </div>

                    <div class="col-md-4">
                        <h1>Bank Information</h1>
                        <p><strong>Bank Name:</strong> {{ $customer->bank_account->bank_name }}</p>
                        <p><strong>Account Title:</strong> {{ $customer->bank_account->account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $customer->bank_account->account_number }}</p>
                        <p><strong>IBAN:</strong> {{ $customer->bank_account->iban }}</p>
                        <p><strong>Swift Code:</strong> {{ $customer->bank_account->swift_code }}</p>


                        <br>
                        <h1>Family and Dependents Information</h1>
                        <p><strong>No of Dependent:</strong> {{ $customer->familyDependent->number_of_dependents }}</p>
                        <p><strong>Spouse Name:</strong> {{ $customer->familyDependent->spouse_name }}</p>
                        <p><strong>Spouse Employment
                                Detail:</strong> {{ $customer->familyDependent->spouse_employment_details }}</p>

                    </div>
                    <div class="col-md-4">
                        <h1>Employment & Financial Information</h1>
                        <p><strong>Employment Status:</strong> {{ $customer->employment->employmentStatus->status }}</p>
                        <p><strong>Income Source:</strong> {{ $customer->employment->incomeSource->source }}</p>
                        <p><strong>Employer/Business Name:</strong> {{ $customer->employment->current_employer }}</p>
                        <p><strong>Position/Role:</strong> {{ $customer->employment->job_title->name }}</p>
                        <p><strong>Years of Employment/Business:</strong> {{ $customer->employment->employment_duration }}</p>
                        <p><strong>How much money do you make each month?</strong> {{ $customer->employment->gross_income }}</p>
                        <p><strong>After earning and spending, how much money do you keep</strong> {{ $customer->employment->net_income }}</p>
                        <p><strong>Existing Loan:</strong> {{ $customer->employment->existingLoan->name }}</p>
                        <br>
                        <h1>Education Background Information</h1>
                        <p><strong>Highest Education:</strong> {{ $customer->education->education->name }}</p>
                        <p><strong>University Name:</strong> {{ $customer->education->university_name }}</p>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6">
                        <h1>Guarantors Contact Information</h1>
                        @foreach($customer->references as $key =>  $row)
                            <p><strong>Guarantors {{$key+1}}</strong></p>

                            <p><strong>Guarantors Name</strong> {{ $row->guarantor_contact_name }}</p>
                            <p><strong>Relationship to Applicant:</strong> {{ $row->relationship->name }}</p>
                            <p><strong>Guarantors Contact:</strong> {{ $row->guarantor_contact_number }}</p>
                            <br>
                        @endforeach

                    </div>
                    <div class="col-md-6">
                        <p><strong>Profile Photo:</strong></p>
                        <a href="{{ asset('storage/' . $customer->profile->photo) }}" target="_blank">
                            <img src="{{ asset('storage/' . $customer->profile->photo) }}" width="50" height="50" alt="Profile Photo"
                                 class="img-thumbnail" style="max-width: 150px;">
                        </a>

                        <p><strong>CNIC Front:</strong></p>
                        <a href="{{ asset('storage/' . $customer->profile->cnic_front) }}" target="_blank">
                            <img src="{{ asset('storage/' . $customer->profile->cnic_front) }}" width="50" height="50" alt="CNIC Front"
                                 class="img-thumbnail" style="max-width: 150px;">
                        </a>

                        <p><strong>CNIC Back:</strong></p>
                        <a href="{{ asset('storage/' . $customer->profile->cnic_back) }}" target="_blank">
                            <img src="{{ asset('storage/' . $customer->profile->cnic_back) }}" width="50" height="50" alt="CNIC Back"
                                 class="img-thumbnail" style="max-width: 150px;">
                        </a>
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
