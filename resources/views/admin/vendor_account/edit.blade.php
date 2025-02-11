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
            <form action="{{route('update-vendor-account', $vendorAccount->id)}}" method="post"
                  name="role_registration" class="flex-fill form-validate-jquery">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Name <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" class="form-control"
                                                   name="name"
                                                   value="{{old('name',$vendorAccount->name)}}"
                                                   placeholder=" Name">
                                            <div class="form-control-feedback">
                                                <i class="icon-user-check text-muted"></i>
                                            </div>
                                            @if ($errors->has('name'))
                                                <span
                                                    class="text-danger">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Email </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="email" name="email"
                                                   class="form-control"
                                                   placeholder="Enter Email Address"
                                                   value="{{session('email') ?? old('email',$vendorAccount->email)}}">
                                            <div class="form-control-feedback">
                                                <i class="icon-mention text-muted"></i>
                                            </div>
                                            @if ($errors->has('email'))
                                                <span
                                                    class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Phone No <span
                                                class="text-danger">*</span> </label>
                                        <input  id="phone_no" value="{{old('phone_no',$vendorAccount->phone)}}" placeholder="Enter Phone No" name="phone_no" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Cnic No <span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('cnic_no',$vendorAccount->cnic_no)}}" name="cnic_no" type="text" class="form-control" placeholder="Enter Cnic No" id="cnic_no" maxlength="15" minlength="15">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Business Name<span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('business_name',$vendorAccount->business_name)}}" name="business_name" type="text" class="form-control" placeholder="Enter Business Name">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Bank Name<span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('bank_name',$vendorAccount->bank_name)}}" name="bank_name" type="text" class="form-control" placeholder="Enter Bank Name">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">IBAN No<span
                                                class="text-danger">*</span> </label>
                                        <input id="iban_no" value="{{old('iban_no',$vendorAccount->iban_no)}}" name="iban_no" type="text" class="form-control" placeholder="Enter IBAN No">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Province<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <select name="province_id" class="form-control select2 province"
                                                    data-type="vendoraccount"   data-placeholder="Select Province">
                                                <option></option>
                                                @foreach($provinces as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('province_id',$vendorAccount->province_id) == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('province_id'))
                                                <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">District<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <select name="district_id" class="form-control select2"
                                                    id="vendoraccount_district"    data-placeholder="Select District">
                                                <option></option>
                                                @foreach($districts as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('district_id',$vendorAccount->district_id) == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('district_id'))
                                                <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">City<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <select name="city_id" class="form-control select2"
                                                    id="vendoraccount_city"   data-placeholder="Select City">
                                                <option></option>
                                                @foreach($cities as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('city_id',$vendorAccount->city_id) == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('city_id'))
                                                <span class="text-danger">{{ $errors->first('city_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Address <span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('address',$vendorAccount->address)}}" name="address" placeholder="Enter Address" type="text" class="form-control">
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
