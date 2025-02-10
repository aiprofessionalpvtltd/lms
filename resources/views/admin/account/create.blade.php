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
            <form action="{{route('store-account')}}" method="post"
                  name="allotee_registration" class="flex-fill form-validate-jquery">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Code <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number"   class="form-control"
                                                   name="code"
                                                   value="{{old('code')}}"
                                                   placeholder="code">

                                            @if ($errors->has('code'))
                                                <span
                                                    class="text-danger">{{ $errors->first('code') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Name <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text"   class="form-control"
                                                   name="name"
                                                   value="{{old('name')}}"
                                                   placeholder="Name">

                                            @if ($errors->has('name'))
                                                <span
                                                    class="text-danger">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Account Name</label>
                                        <div class="form-group">
                                            <select name="account_name_id" class="form-control select2"
                                                    data-placeholder="Select Account Name">
                                                <option></option>
                                                @foreach($names as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('account_name_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('account_name_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('account_name_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Account Type</label>
                                        <div class="form-group">
                                            <select name="account_type_id" class="form-control select2"
                                                    data-placeholder="Select Account Name">
                                                <option></option>
                                                @foreach($types as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('account_type_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('account_type_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('account_type_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Balance <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number"   class="form-control"
                                                   name="balance"
                                                   value="{{old('balance')}}"
                                                   placeholder="Balance">

                                            @if ($errors->has('balance'))
                                                <span
                                                    class="text-danger">{{ $errors->first('balance') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="col-form-label">Parent ID</label>
                                        <div class="form-group">
                                            <select name="parent_id" class="form-control select2"
                                                    data-placeholder="Select Parent Account">
                                                <option></option>
                                                @foreach($accounts as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('parent_id') == $row->id ? 'selected' : '' }}>{{ $row->code.' ' . $row->accountName->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('parent_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('parent_id') }}</span>
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
