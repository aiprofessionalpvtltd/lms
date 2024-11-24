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

            <form action="{{ route('recovery.store') }}" method="post" name="allotee_registration"
                  class="flex-fill form-validate-jquery">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Installment Month<span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select
                                                data-placeholder="Select Installment Month"
                                                name="installment_detail_id"
                                                id="installment_detail_id"
                                                class="form-control select2"
                                                data-fouc>
                                                <option></option>
                                                @foreach($installmentDetails as $row)
                                                    <option value="{{ $row->id }}"
                                                            data-overdue-days="{{ $row->overdue_days }}"
                                                            data-total-amount="{{ $row->total_amount }}"
                                                            data-late-fee="{{ $row->late_fee }}">
                                                        {{ $row->installment_number . ' (' . $row->due_date . ')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('installment_detail_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('installment_detail_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Overdue Days</label>
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control" id="overdue_days"
                                                   placeholder="Overdue Days">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Late Fee</label>
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control" id="late_fee"
                                                   placeholder="Late Fee">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Amount <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number" required class="form-control" name="amount"
                                                   value="{{ old('amount') }}" placeholder="Amount">
                                            @if ($errors->has('amount'))
                                                <span class="text-danger">{{ $errors->first('amount') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Payment Method <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Payment Method" name="payment_method"
                                                    id="payment_method" class="form-control select2" data-fouc>
                                                <option></option>
                                                <option value="bank">Bank</option>
                                                <option value="cash">Cash</option>
                                                <option value="jazzcash">Jazz Cash</option>
                                                <option value="easypaisa">Easy Paisa</option>
                                            </select>
                                            @if ($errors->has('payment_method'))
                                                <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Remarks</label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <textarea name="remarks" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-5">
                                        <button type="submit" class="btn btn-outline-primary float-end">
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
    <script>
        $(document).ready(function () {
            $('#installment_detail_id').on('change', function () {
                // Get the selected option
                const selectedOption = $(this).find('option:selected');

                // Extract overdue days and late fee
                let totalAmount = Math.abs(selectedOption.data('total-amount')) || 0; // Ensure positive value
                let overdueDays = Math.abs(selectedOption.data('overdue-days')) || 0; // Ensure positive value
                let lateFee = Math.abs(selectedOption.data('late-fee')) || 0; // Ensure positive value


                // Populate the fields
                $('#overdue_days').val(overdueDays.toFixed());
                $('#late_fee').val(lateFee.toFixed());
                $('input[name="amount"]').val(totalAmount.toFixed()); // Update the amount field
            });

            // Initialize Select2
            $('.select2').select2();
        });


    </script>

@endpush
