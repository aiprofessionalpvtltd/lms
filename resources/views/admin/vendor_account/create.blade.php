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
            <form action="{{route('store-vendor-account')}}" method="post"
                  name="allotee_registration" class="flex-fill form-validate-jquery">
                @csrf

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
                                                   value="{{old('name')}}"
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
                                                   value="{{session('email') ?? old('email')}}">
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
                                        <input  id="phone_no" value="{{old('phone_no')}}" placeholder="Enter Phone No" name="phone_no" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Cnic No <span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('cnic_no')}}" name="cnic_no" type="text" class="form-control" placeholder="Enter Cnic No" id="cnic_no" maxlength="15" minlength="15">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Business Name<span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('business_name')}}" name="business_name" type="text" class="form-control" placeholder="Enter Business Name">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Bank Name<span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('bank_name')}}" name="bank_name" type="text" class="form-control" placeholder="Enter Bank Name">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">IBAN No<span
                                                class="text-danger">*</span> </label>
                                        <input id="iban_no" value="{{old('iban_no')}}" name="iban_no" type="text" class="form-control" placeholder="Enter IBAN No">
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
                                                        value="{{ $row->id }}" {{ old('province_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
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
                                                {{--                                                @foreach($districts as $row)--}}
                                                {{--                                                    <option--}}
                                                {{--                                                        value="{{ $row->id }}" {{ old('district_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>--}}
                                                {{--                                                @endforeach--}}
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
                                                {{--                                                @foreach($cities as $row)--}}
                                                {{--                                                    <option--}}
                                                {{--                                                        value="{{ $row->id }}" {{ old('city_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>--}}
                                                {{--                                                @endforeach--}}
                                            </select>
                                            @if ($errors->has('city_id'))
                                                <span class="text-danger">{{ $errors->first('city_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Address <span
                                                class="text-danger">*</span> </label>
                                        <input value="{{old('address')}}" name="address" placeholder="Enter Address" type="text" class="form-control">
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit"
                                                class="btn btn-outline-primary float-end">
                                            Save
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
    <script>
        Inputmask("99999-9999999-9").mask("#cnic_no");
        Inputmask("0399-9999999").mask("#phone_no");
        Inputmask({
            mask: "AA99 AAAA 9999 9999 9999 9999", // Adjust the mask for IBAN length
            placeholder: "_", // Placeholder character
            definitions: {
                'A': {
                    validator: "[A-Za-z]", // Letters only
                    casing: "upper" // Automatically converts to uppercase
                },
                '9': {
                    validator: "[0-9]" // Numbers only
                }
            }
        }).mask("#iban_no");


    </script>
    <script>
        $(document).ready(function () {

            // Initialize Select2
            $('.select2').select2();
            // Flatpickr
            flatpickr(".flatpickr-minimum");
        });
    </script>

@endpush
