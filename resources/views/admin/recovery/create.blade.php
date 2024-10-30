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

            <form action="{{ route('recovery.store') }}" method="post"
                  name="allotee_registration" class="flex-fill form-validate-jquery">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Installment Month<span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Payment Method"
                                                    name="installment_detail_id" id="installment_detail_id"
                                                    class="form-control select2"
                                                    data-fouc>
                                                @foreach($installmentDetails as $row)
                                                    <option value="{{$row->id}}">{{$row->amount_due . ' (' .$row->due_date .')'}}</option>
                                                @endforeach

                                            </select>
                                            @if ($errors->has('installment_detail_id'))
                                                <span class="text-danger">{{ $errors->first('installment_detail_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number" required class="form-control"
                                                   name="amount"
                                                   value="{{old('amount')}}"
                                                   placeholder=" amount">
                                            <div class="form-control-feedback">
                                                <i class="icon-role-check text-muted"></i>
                                            </div>
                                            @if ($errors->has('amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('amount') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Payment Method <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Payment Method"
                                                    name="payment_method" id="payment_method"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                <option value="bank">Bank</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                            @if ($errors->has('payment_method'))
                                                <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Remarks <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group form-group-feedback form-group-feedback-right">
                                            <textarea name="remarks" class="form-control"></textarea>

                                        </div>
                                    </div>

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
