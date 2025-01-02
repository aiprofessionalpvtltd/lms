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
                <h4><span class="font-weight-semibold"></span>Edit Loan Application
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
            <form action="{{route('update-loan-application',$loanApplication->id)}}" method="post"
                  enctype="multipart/form-data"
                  name="allotee_registration" class="flex-fill form-validate-jquery">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Select Customer <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Customer"
                                                    name="customer_id" id="customer_id"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($customers as $key => $row)
                                                    <option
                                                        {{$loanApplication->user_id == $row->id ? 'selected' : ''}} value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('customer_id'))
                                                <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">Request For <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Option"
                                                    name="request_for" id="request_for"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                <option
                                                    {{  $loanApplication->product_id != null ? 'selected' : ''}} value="product">
                                                    Product Financing
                                                </option>
                                                <option
                                                    {{$loanApplication->product_id == null ? 'selected' : ''}} value="loan">
                                                    Standard Loan
                                                </option>

                                            </select>
                                            @if ($errors->has('request_for'))
                                                <span class="text-danger">{{ $errors->first('request_for') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if( $loanApplication->product_id != null)
                                        <div class="col-md-4" id="productDiv">
                                            <label class="col-form-label">Select Product <span
                                                    class="text-danger">*</span></label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <select data-placeholder="Select Product"
                                                        name="product_id" id="product_id"
                                                        class="form-control select2"
                                                        data-fouc>
                                                    <option></option>
                                                    @foreach($products as $key => $row)
                                                        <option  {{$loanApplication->calculatedProduct->product_id == $row->id  ? 'selected' : ''}}  data-price="{{$row->price}}"
                                                                data-interest="{{$row->interest_rate}}"
                                                                value="{{ $row->id }}">{{ $row->name .'(' . $row->price .')'}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('product_id'))
                                                    <span class="text-danger">{{ $errors->first('product_id') }}</span>
                                                @endif


                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-4" id="amountDiv">
                                        <label class="col-form-label  ">Loan Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="loan_amount" id="loan_amount"
                                                   value="{{$loanApplication->loan_amount}}"
                                                   placeholder="Loan Amount">

                                            @if ($errors->has('loan_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('loan_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="InterestDiv">
                                        <label class="col-form-label  ">Interest <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" class="form-control"
                                                   name="old_interest_rate" id="interest_rate"
                                                   value="{{$loanApplication->calculatedProduct->interest_rate_percentage}}"
                                                   placeholder="Interest Rate">

                                            @if ($errors->has('interest_rate'))
                                                <span
                                                    class="text-danger">{{ $errors->first('interest_rate') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Old Processing
                                            Fee <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" class="form-control"
                                                   name="old_processing_fee_amount" id="old_processing_fee_amount"
                                                   value="{{$loanApplication->calculatedProduct->processing_fee_percentage}}"
                                                   placeholder="Processing Fee">

                                            @if ($errors->has('old_processing_fee_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('old_processing_fee_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if( $loanApplication->product_id != null)
                                    <div class="col-md-4" id="downPaymentDiv">
                                        <label class="col-form-label">Down Payment (%) <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Option"
                                                    name="down_payment_percentage" id="down_payment_percentage"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                <option
                                                    {{$loanApplication->calculatedProduct->down_payment_percentage == '10' ? 'selected' : ''}} value="10">
                                                    10%
                                                </option>
                                                <option
                                                    {{$loanApplication->calculatedProduct->down_payment_percentage == '20' ? 'selected' : ''}}  value="20">
                                                    20%
                                                </option>
                                                <option
                                                    {{$loanApplication->calculatedProduct->down_payment_percentage == '30' ? 'selected' : ''}}  value="30">
                                                    30%
                                                </option>
                                                <option
                                                    {{$loanApplication->calculatedProduct->down_payment_percentage == '40' ? 'selected' : ''}}  value="40">
                                                    40%
                                                </option>
                                                <option
                                                    {{$loanApplication->calculatedProduct->down_payment_percentage == '50' ? 'selected' : ''}}  value="50">
                                                    50%
                                                </option>
                                            </select>
                                            @if ($errors->has('down_payment_percentage'))
                                                <span
                                                    class="text-danger">{{ $errors->first('down_payment_percentage') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <label class="col-form-label">Select Loan Duration <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Duration"
                                                    name="loan_duration_id" id="loan_duration_id"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($loanDurations as $key => $row)
                                                    <option  {{$loanApplication->calculatedProduct->loan_duration_id == $row->value  ? 'selected' : ''}}
                                                             value="{{ $row->value }}">{{ $row->name}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('loan_duration_id'))
                                                <span
                                                    class="text-danger">{{ $errors->first('loan_duration_id') }}</span>
                                            @endif


                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="calculationDiv">
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Finance Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="finance_amount" readonly
                                                   value="{{$loanApplication->calculatedProduct->financed_amount}}"
                                                   placeholder="Finance Amount">

                                            @if ($errors->has('finance_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('finance_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Processing
                                            Fee <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="processing_fee_amount" readonly
                                                   value="{{$loanApplication->calculatedProduct->processing_fee_amount}}"
                                                   placeholder="Processing Fee">

                                            @if ($errors->has('processing_fee_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('processing_fee_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Disbursement Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="disbursement_amount" readonly
                                                   value="{{$loanApplication->calculatedProduct->disbursement_amount}}"
                                                   placeholder="Disbursement Amount ">

                                            @if ($errors->has('disbursement_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('disbursement_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Interest Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="total_interest_amount" readonly
                                                   value="{{$loanApplication->calculatedProduct->total_interest_amount}}"
                                                   placeholder="Interest Amount ">

                                            @if ($errors->has('total_interest_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('total_interest_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Payable Amount <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="total_repayable_amount" readonly
                                                   value="{{$loanApplication->calculatedProduct->total_repayable_amount}}"
                                                   placeholder="Interest Amount ">

                                            @if ($errors->has('total_repayable_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('total_repayable_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label  ">Monthly Installment <span
                                                class="text-danger">*</span> </label>
                                        <div
                                            class="form-group">
                                            <input type="text" required class="form-control"
                                                   name="monthly_installment_amount" readonly
                                                   value="{{$loanApplication->calculatedProduct->monthly_installment_amount}}"
                                                   placeholder="Monthly Installment">

                                            @if ($errors->has('monthly_installment_amount'))
                                                <span
                                                    class="text-danger">{{ $errors->first('monthly_installment_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="col-form-label">Select Loan Purpose <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Purpose"
                                                    name="loan_purpose_id" id="loan_purpose_id"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($loanPurposes as $key => $row)
                                                    <option  {{$loanApplication->loan_purpose_id == $row->id ? 'selected' : ''}}  value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('loan_purpose_id'))
                                                <span class="text-danger">{{ $errors->first('loan_purpose_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label" for="attachment_file">Bank Statement</label>
                                        <div class="form-group">
                                            <input type="file" name="bank_document" class="form-control">
                                            @if ($errors->has('bank_document'))
                                                <span class="text-danger">{{ $errors->first('bank_document') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label" for="attachment_file">Salary Slip</label>
                                        <div class="form-group">
                                            <input type="file" name="salary_slip_document" class="form-control">
                                            @if ($errors->has('salary_slip_document'))
                                                <span
                                                    class="text-danger">{{ $errors->first('salary_slip_document') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <h4>Uploaded Attachment</h4>
                                    <div class="col-md-6">
                                        @if(count($loanApplication->attachments) > 0)
                                            @foreach($loanApplication->attachments as $attachment)
                                                <p><strong>{{ $attachment->documentType->name }}</strong></p>
                                                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $attachment->path) }}"
                                                         alt="{{ $attachment->documentType->name }}" class="img-thumbnail"
                                                         style="max-width: 150px;">
                                                </a>
                                            @endforeach
                                        @else
                                            <p><strong>No Documents Uploaded</strong></p>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-12 mt-3">
                                    <button type="submit"
                                            class="btn btn-outline-primary float-end">
                                        Update
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- /registration form -->
    </div>
    <!-- /form validation -->

    </div>
    </div>
    <!-- /content area -->
    <!--**********************************
        Content body end
    ***********************************-->

@endsection

@push('script')

    <script>
        $(document).ready(function () {
            // $('#calculationDiv').hide(); // Initially hide the calculation div

            $('#request_for').change(function (e) {
                e.preventDefault();
                let requestFor = $(this).val();

                console.log('request_for')
                // Reset loan amount field
                // $('#loan_amount').val('').prop('readonly', false);
                // $('#interest_rate').val('').prop('readonly', false);


                if (requestFor === 'product') {
                    $('#productDiv').show(); // Show the product dropdown
                    // $('#amountDiv').hide(); // Hide the loan amount input
                    $('#downPaymentDiv').show(); // Hide the product dropdown
                    $('.select2').select2(); // Reinitialize Select2 if needed

                } else {
                    $('#productDiv').hide(); // Hide the product dropdown
                    $('#downPaymentDiv').hide(); // Hide the product dropdown
                    $('#amountDiv').show(); // Show the loan amount input
                    $('#interest_rate').val(35);

                }
            }).trigger('change');

            $('#product_id').on('change',function (e) {
                e.preventDefault();
                console.log('product_id')

                // Get the data-price attribute of the selected option
                let productPrice = $(this).find(':selected').data('price');
                let productInterest = $(this).find(':selected').data('interest');

                if (productPrice) {
                    // Set loan amount to the product price and make it readonly
                    $('#loan_amount').val(productPrice).prop('readonly', true);
                    $('#interest_rate').val(productInterest);
                } else {
                    // Clear loan amount if no product is selected
                    $('#loan_amount').val('').prop('readonly', false);
                    $('#interest_rate').val('').prop('readonly', false);
                }
            }); // Trigger the change event to handle preselected value
        });


        function calculateLoan() {
            let loanAmount = $('input[name="loan_amount"]').val();
            let months = $('#loan_duration_id').val();
            let requestFor = $('#request_for').val();
            let downPaymentPercentage = $('#down_payment_percentage').val();
            let old_interest_rate = $('#interest_rate').val();
            let old_processing_fee_amount = $('#old_processing_fee_amount').val();
            let product_id = $('#product_id').find(':selected').val()
            if (loanAmount && months  && requestFor) {
                $.ajax({
                    url: '{{ route('calculate-loan-application') }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        loan_amount: loanAmount,
                        months: months,
                        request_for: requestFor,
                        down_payment_percentage: downPaymentPercentage,
                        old_interest_rate: old_interest_rate,
                        old_processing_fee_amount: old_processing_fee_amount,
                        product_id: product_id,
                    },
                    success: function (response) {
                        response = response.data;
                        $('input[name="finance_amount"]').val(response.financed_amount);
                        $('input[name="processing_fee_amount"]').val(response.processing_fee_amount);
                        $('input[name="disbursement_amount"]').val(response.disbursement_amount);
                        $('input[name="total_interest_amount"]').val(response.total_interest_amount);
                        $('input[name="total_repayable_amount"]').val(response.total_repayable_amount);
                        $('input[name="monthly_installment_amount"]').val(response.monthly_installment_amount);
                        $('#calculationDiv').show(); // Show the calculation div
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Error calculating loan details. Please try again.');
                    }
                });
            }
        }

        $(document).on('change', '#loan_duration_id,#interest_rate,#old_processing_fee_amount, #down_payment_percentage,  input[name="loan_amount"]', function () {
              calculateLoan();
        });


    </script>

@endpush
