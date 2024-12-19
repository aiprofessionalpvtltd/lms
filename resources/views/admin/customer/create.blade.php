@extends('admin.layouts.app')
@push('style')
    <style>
        .select2{
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{ $title }}</span></h4>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content">
        <!-- Form validation -->
        <div class="card">
            <!-- Company form -->
            <form action="{{ route('store-customer') }}" method="POST" enctype="multipart/form-data"
                  class="flex-fill form-validate-jquery">
                @csrf
                <div class="card-body">

                    <!-- Tab navigation -->
                    <ul class="nav nav-tabs ">
                        <li class="nav-item">
                            <a href="#general-tab" class="nav-link active" data-bs-toggle="tab">General</a>
                        </li>
                        <li class="nav-item">
                            <a href="#attachments-tab" class="nav-link" data-bs-toggle="tab">Attachments</a>
                        </li>
                        <li class="nav-item">
                            <a href="#bank-tab" class="nav-link" data-bs-toggle="tab">Bank Information</a>
                        </li>
                        <li class="nav-item">
                            <a href="#employment-tab" class="nav-link" data-bs-toggle="tab">Employment Information</a>
                        </li>

                        <li class="nav-item">
                            <a href="#guarantor-tab" class="nav-link" data-bs-toggle="tab">Guarantor Information</a>
                        </li>

                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="general-tab">


                            <fieldset class="border p-3 mb-4">
                                <legend class="mb-3 azm-color-444">User Information</legend>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="col-form-label">First Name<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="first_name" class="form-control"
                                                   placeholder="First Name"
                                                   value="{{ old('first_name') }}">
                                            @if ($errors->has('first_name'))
                                                <span class="text-danger">  {{ $errors->first('first_name') }}</span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Last Name<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="last_name" class="form-control"
                                                   placeholder="Last Name"
                                                   value="{{ old('last_name') }}">
                                            @if ($errors->has('last_name'))
                                                <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-4">
                                        <label class="col-form-label">Email</label>
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" placeholder="Email"
                                                   value="{{ old('email') }}">
                                            @if ($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-4">
                                        <label class="col-form-label">Province</label>
                                        <div class="form-group">
                                            <select name="province_id" class="form-control select2"
                                                    data-placeholder="Select Province">
                                                <option></option>
                                                @foreach($provinces as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('province_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('province_id'))
                                                <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">City</label>
                                        <div class="form-group">
                                            <select name="city_id" class="form-control select2"
                                                    data-placeholder="Select City">
                                                <option></option>
                                                @foreach($cities as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('city_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('city_id'))
                                                <span class="text-danger">{{ $errors->first('city_id') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="col-form-label">District</label>
                                        <div class="form-group">
                                            <select name="district_id" class="form-control select2"
                                                    data-placeholder="Select District">
                                                <option></option>
                                                @foreach($districts as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('district_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('district_id'))
                                                <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </fieldset>


                            <fieldset class="border p-3 mb-4">
                                <legend class="mb-3 azm-color-444">User Profile</legend>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Father Name<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="father_name" class="form-control"
                                                   placeholder="Father Name"
                                                   value="{{ old('father_name') }}">
                                            @if ($errors->has('father_name'))
                                                <span class="text-danger">{{ $errors->first('father_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Date Of Birth <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="dob" class="form-control flatpickr-minimum"
                                                   placeholder="Select DOB " value="{{old('dob' , currentDateInsert())}}"/>
                                            @if ($errors->has('dob'))
                                                <span class="text-danger">{{ $errors->first('dob') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">CNIC # <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="cnic_no" class="form-control"
                                                   placeholder="99999-9999999-9"
                                                   data-inputmask="'mask': '99999-9999999-9'"
                                                   value="{{ old('cnic_no') }}">
                                            @if ($errors->has('cnic_no'))
                                                <span class="text-danger">{{ $errors->first('cnic_no') }}</span>
                                            @endif

                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="col-form-label">CNIC Issue Date <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="issue_date" class="form-control flatpickr-minimum"
                                                   placeholder="Select Date " value="{{ old('issue_date',currentDateInsert())}}"/>
                                            @if ($errors->has('issue_date'))
                                                <span class="text-danger">{{ $errors->first('issue_date') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="col-form-label">CNIC Expiry Date <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="expire_date" class="form-control flatpickr-minimum"
                                                   placeholder="Select Date " value="{{ old('expire_date',currentDateInsert())}}"/>
                                            @if ($errors->has('expire_date'))
                                                <span class="text-danger">{{ $errors->first('expire_date') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Mobile No <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="mobile_no" class="form-control"
                                                   placeholder="0399-9999999"
                                                   data-inputmask="'mask': '0399-9999999'"
                                                   value="{{ old('mobile_no') }}">
                                            @if ($errors->has('mobile_no'))
                                                <span class="text-danger">{{ $errors->first('mobile_no') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="col-form-label">Alternate Mobile No <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="alternate_mobile_no" class="form-control"
                                                   placeholder="0399-9999999"
                                                   data-inputmask="'mask': '0399-9999999'"
                                                   value="{{ old('alternate_mobile_no') }}">
                                            @if ($errors->has('alternate_mobile_no'))
                                                <span
                                                    class="text-danger">{{ $errors->first('alternate_mobile_no') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Gender</label>
                                        <div class="form-group">
                                            <select name="gender_id" class="form-control select2"
                                                    data-placeholder="Select Gender">
                                                <option></option>
                                                @foreach($genders as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('gender_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('gender_id'))
                                                <span class="text-danger">{{ $errors->first('gender_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Marital Status</label>
                                        <div class="form-group">
                                            <select name="marital_status_id" class="form-control select2"
                                                    data-placeholder="Select Marital Status">
                                                <option></option>
                                                @foreach($maritalStatuses as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('marital_status_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('marital_status_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('marital_status_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Nationality</label>
                                        <div class="form-group">
                                            <select name="nationality_id" class="form-control select2"
                                                    data-placeholder="Select Nationality">
                                                <option></option>
                                                @foreach($nationalities as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('nationality_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('nationality_id'))
                                                <span class="text-danger">{{ $errors->first('nationality_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Residence Type</label>
                                        <div class="form-group">
                                            <select name="residence_type_id" class="form-control select2"
                                                    data-placeholder="Select Residence Type">
                                                <option></option>
                                                @foreach($residenceTypes as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('residence_type_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('residence_type_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('residence_type_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Residence Duration</label>
                                        <div class="form-group">
                                            <select name="residence_duration_id" class="form-control select2"
                                                    data-placeholder="Select Residence Duration">
                                                <option></option>
                                                @foreach($residenceDurations as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('residence_duration_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('residence_duration_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('residence_duration_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Current Address Duration</label>
                                        <div class="form-group">
                                            <select name="current_address_duration" class="form-control select2"
                                                    data-placeholder="Select Current Address Duration">
                                                <option></option>
                                                @foreach($residenceDurations as $row)
                                                    <option
                                                        value="{{ $row->name }}" {{ old('current_address_duration') == $row->name ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('current_address_duration'))
                                                <span
                                                    class="text-danger">{{ $errors->first('current_address_duration') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-form-label">Permanent Address</label>
                                        <div class="form-group">
                                            <textarea name="permanent_address" class="form-control">{{old('permanent_address')}}</textarea>
                                            @if ($errors->has('permanent_address'))
                                                <span
                                                    class="text-danger">{{ $errors->first('permanent_address') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">Current Address</label>
                                        <div class="form-group">
                                            <textarea name="current_address" class="form-control">{{old('current_address')}}</textarea>
                                            @if ($errors->has('residence_duration_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('residence_duration_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="border p-3 mb-4">
                                <legend class="mb-3 azm-color-444">Education</legend>
                                <div class="row mt-3">


                                    <div class="col-md-4">
                                        <label class="col-form-label">Education Level</label>
                                        <div class="form-group">
                                            <select name="education_id" class="form-control select2"
                                                    data-placeholder="Select Education Level">
                                                <option></option>
                                                @foreach($educations as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('education_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('education_id'))
                                                <span class="text-danger">{{ $errors->first('education_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">University Name<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="university_name" class="form-control"
                                                   placeholder="University Name"
                                                   value="{{ old('university_name') }}">
                                            @if ($errors->has('university_name'))
                                                <span class="text-danger">{{ $errors->first('university_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </fieldset>
                            <fieldset class="border p-3 mb-4">
                                <legend class="mb-3 azm-color-444">Family dependent</legend>
                                <div class="row">


                                    <div class="col-md-4">
                                        <label class="col-form-label">No Of Dependent<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="number_of_dependents" class="form-control"
                                                   placeholder="No Of Dependent"
                                                   value="{{ old('number_of_dependents') }}">
                                            @if ($errors->has('number_of_dependents'))
                                                <span
                                                    class="text-danger">{{ $errors->first('number_of_dependents') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Spouse Name</label>
                                        <div class="form-group">
                                            <input type="text" name="spouse_name" class="form-control"
                                                   placeholder="Spouse Name"
                                                   value="{{ old('spouse_name') }}">
                                            @if ($errors->has('spouse_name'))
                                                <span class="text-danger">{{ $errors->first('spouse_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Spouse Employment Detail</label>
                                        <div class="form-group">
                                            <input type="text" name="spouse_employment_details" class="form-control"
                                                   placeholder="Spouse Employment Detail"
                                                   value="{{ old('spouse_employment_details') }}">
                                            @if ($errors->has('spouse_employment_details'))
                                                <span
                                                    class="text-danger">{{ $errors->first('spouse_employment_details') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                            </fieldset>

                        </div>

                        <div class="tab-pane fade" id="attachments-tab">
                            <div class="row mt-3">
                                <div id="attachments-container"></div>
                                <!-- Hidden Template -->
                                <div class="attachment-row mt-5">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="attachment_file">CNIC Front</label>
                                                <input type="file" name="cnic_front" class="form-control">
                                                @if ($errors->has('cnic_front'))
                                                    <span class="text-danger">{{ $errors->first('cnic_front') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="attachment_file">CNIC Back</label>
                                                <input type="file" name="cnic_back" class="form-control">
                                                @if ($errors->has('cnic_back'))
                                                    <span class="text-danger">{{ $errors->first('cnic_back') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <div class="form-group">
                                                <label for="attachment_file">Photo</label>
                                                <input type="file" name="photo" class="form-control">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="bank-tab">

                            <fieldset>
                                <div class="row">

                                    <div class="col-md-4">
                                        <label class="col-form-label">Bank</label>
                                        <div class="form-group">
                                            <select name="bank_name" class="form-control select2"
                                                    data-placeholder="Select Bank">
                                                <option></option>
                                                @foreach($banks as $row)
                                                    <option
                                                        value="{{ $row->name }}" {{ old('bank_name') == $row->name ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('bank_name'))
                                                <span
                                                    class="text-danger">{{ $errors->first('bank_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Account Name<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="account_name" class="form-control"
                                                   placeholder="Account Name"
                                                   value="{{ old('account_name') }}">
                                            @if ($errors->has('account_name'))
                                                <span class="text-danger">{{ $errors->first('account_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Account Number<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="account_number" class="form-control"
                                                   placeholder="Account Number"
                                                   value="{{ old('account_number') }}">
                                            @if ($errors->has('account_number'))
                                                <span class="text-danger">{{ $errors->first('account_number') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">IBAN<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="iban" class="form-control"
                                                   placeholder="IBAN"
                                                   value="{{ old('iban') }}">
                                            @if ($errors->has('iban'))
                                                <span class="text-danger">{{ $errors->first('iban') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Swift Code<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="number" name="swift_code" class="form-control"
                                                   placeholder="Swift Code"
                                                   value="{{ old('swift_code') }}">
                                            @if ($errors->has('swift_code'))
                                                <span class="text-danger">{{ $errors->first('swift_code') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="tab-pane fade" id="employment-tab">


                            <div class="row">

                                <div class="col-md-4">
                                    <label class="col-form-label">Employment Status</label>
                                    <div class="form-group">
                                        <select name="employment_status_id" class="form-control select2"
                                                data-placeholder="Select Employment Status">
                                            <option></option>
                                            @foreach($employmentStatus as $row)
                                                <option
                                                    value="{{ $row->id }}" {{ old('employment_status_id') == $row->id ? 'selected' : '' }}>{{ $row->status }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('employment_status_id'))
                                            <span
                                                class="text-danger">{{ $errors->first('employment_status_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-form-label">Income Source</label>
                                    <div class="form-group">
                                        <select name="income_source_id" class="form-control select2"
                                                data-placeholder="Select Income Source">
                                            <option></option>
                                            @foreach($incomeSources as $row)
                                                <option
                                                    value="{{ $row->id }}" {{ old('income_source_id') == $row->id ? 'selected' : '' }}>{{ $row->source }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('income_source_id'))
                                            <span
                                                class="text-danger">{{ $errors->first('income_source_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-form-label">Current Employer<span
                                            class="text-danger">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="current_employer" class="form-control"
                                               placeholder="Current Employer"
                                               value="{{ old('current_employer') }}">
                                        @if ($errors->has('current_employer'))
                                            <span class="text-danger">{{ $errors->first('current_employer') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-form-label">Employment Duration<span
                                            class="text-danger">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="employment_duration" class="form-control"
                                               placeholder="Employment Duration"
                                               value="{{ old('employment_duration') }}">
                                        @if ($errors->has('employment_duration'))
                                            <span class="text-danger">{{ $errors->first('employment_duration') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-form-label">Job Title<span
                                            class="text-danger">*</span></label>
                                    <select name="job_title_id" class="form-control select2"
                                            data-placeholder="Select Job Title">
                                        <option></option>
                                        @foreach($jobs as $row)
                                            <option
                                                value="{{ $row->id }}" {{ old('job_title_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('job_title_id'))
                                        <span
                                            class="text-danger">{{ $errors->first('job_title_id') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <label class="col-form-label">Gross Income<span
                                            class="text-danger">*</span></label>
                                    <div class="form-group">
                                        <input type="number" name="gross_income" class="form-control"
                                               placeholder="Gross Income"
                                               value="{{ old('gross_income') }}">
                                        @if ($errors->has('gross_income'))
                                            <span class="text-danger">{{ $errors->first('gross_income') }}</span>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <label class="col-form-label">Net Income<span
                                            class="text-danger">*</span></label>
                                    <div class="form-group">
                                        <input type="number" name="net_income" class="form-control"
                                               placeholder="Net Income"
                                               value="{{ old('net_income') }}">
                                        @if ($errors->has('net_income'))
                                            <span class="text-danger">{{ $errors->first('net_income') }}</span>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <label class="col-form-label">Existing Loan</label>
                                    <div class="form-group">
                                        <select name="existing_loans_id" class="form-control select2"
                                                data-placeholder="Select Existing Loan">
                                            <option></option>
                                            @foreach($existingLoan as $row)
                                                <option
                                                    value="{{ $row->id }}" {{ old('existing_loans_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('existing_loans_id'))
                                            <span
                                                class="text-danger">{{ $errors->first('existing_loans_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="tab-pane fade" id="guarantor-tab">
                            <div class="form-check mt-5 mb-3">
                                <input type="checkbox" class="form-check-input" id="noGuarantorCheckbox">
                                <label class="form-check-label" for="noGuarantorCheckbox">No Need for Guarantor</label>
                            </div>

                            <div id="guarantor-fields">
                                <!-- First Guarantor -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Relationship</label>
                                        <div class="form-group">
                                            <select name="relationship_id[]" class="form-control select2"
                                                    data-placeholder="Select Relationship 1">
                                                <option></option>
                                                @foreach($relationships as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('relationship_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Guarantor Name<span class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="guarantor_contact_name[]" class="form-control"
                                                   placeholder="Guarantor Name 1" value="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Mobile No <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="guarantor_contact_number[]" class="form-control"
                                                   placeholder="0399-9999999" data-inputmask="'mask': '0399-9999999'" value="">
                                        </div>
                                    </div>
                                </div>

                                <!-- Second Guarantor -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Relationship</label>
                                        <div class="form-group">
                                            <select name="relationship_id[]" class="form-control select2"
                                                    data-placeholder="Select Relationship 2">
                                                <option></option>
                                                @foreach($relationships as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('relationship_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Guarantor Name<span class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="guarantor_contact_name[]" class="form-control"
                                                   placeholder="Guarantor Name 2" value="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Mobile No <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="guarantor_contact_number[]" class="form-control"
                                                   placeholder="0399-9999999" data-inputmask="'mask': '0399-9999999'" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit button -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-end m-5">Save</button>
                </div>
            </form>
            <!-- /company form -->
        </div>
        <!-- /form validation -->
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('#noGuarantorCheckbox').change(function () {
                if ($(this).is(':checked')) {
                    // Disable all fields inside #guarantor-fields
                    $('#guarantor-fields').find('input, select').prop('disabled', true);
                } else {
                    // Enable all fields inside #guarantor-fields
                    $('#guarantor-fields').find('input, select').prop('disabled', false);
                }
            });
        });


        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Select2
            $('.select2').select2();
            // Flatpickr
            flatpickr(".flatpickr-minimum");
        });
    </script>
@endpush
