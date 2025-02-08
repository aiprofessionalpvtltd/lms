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
            <form action="{{route('update-account', $account->id)}}" method="post"
                  name="role_registration" class="flex-fill form-validate-jquery">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-4">
                                        <label class="col-form-label">Account Name</label>
                                        <div class="form-group">
                                            <select name="account_name_id" class="form-control select2"
                                                    data-placeholder="Select Account Name">
                                                <option></option>
                                                @foreach($names as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{$account->account_name_id == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('account_name_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('account_name_id') }}</span>
                                            @endif
                                        </div>
                                    </div>



                                </div>`
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
