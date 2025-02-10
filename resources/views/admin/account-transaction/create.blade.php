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
            <form action="{{route('store-account-transaction')}}" method="post" id="transaction-form"
                   class="flex-fill form-validate-jquery">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-form-label">Date <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="date" class="form-control flatpickr-minimum"
                                                   placeholder="Select Date "
                                                   value="{{old('date' , currentDateInsert())}}"/>
                                            @if ($errors->has('date'))
                                                <span class="text-danger">{{ $errors->first('date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-3"> {{$title}}</h3>

                                <div class="container">
                                    <div id="transaction-rows">
                                        <!-- Default 5 rows -->
                                        @for($i = 0; $i < 5; $i++)
                                            <div class="row transaction-row">
                                                <div class="col-md-3">
                                                    <label class="col-form-label">Account Head</label>
                                                    <div class="form-group">
                                                        <select name="account_id[{{ $i }}]" class="form-control select2"
                                                                data-placeholder="Select Account">
                                                            <option></option>
                                                            @foreach($accounts as $row)
                                                                <option value="{{ $row->id }}">
                                                                    {{ $row->name . ' ('. $row->accountType->name.')' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="col-form-label">Debit</label>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control debit-input" name="debit_amount[{{ $i }}]" value="0">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="col-form-label">Credit</label>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control credit-input" name="credit_amount[{{ $i }}]" value="0">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="col-form-label">Journal Entry No</label>
                                                    <div class="form-group">
                                                        <input type="text" name="reference[{{ $i }}]" class="form-control" placeholder="Reference">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="col-form-label">Description</label>
                                                    <div class="form-group">
                                                        <input type="text" name="description[{{ $i }}]" class="form-control" placeholder="Description">
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                    <label class="col-form-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <button type="button" class="btn btn-primary mt-3" id="add-row">Add More</button>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <strong>Total</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Debit: PKR <span id="total-debit">0.00</span></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Credit: PKR <span id="total-credit">0.00</span></strong>
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
            let rowIndex = 5; // Start with 5 default rows

            // Initialize Select2 and Flatpickr on page load
            $('.select2').select2();
            flatpickr(".flatpickr-minimum");

            // Function to add a new row
            $("#add-row").click(function () {
                let newRow = `
            <div class="row transaction-row">
                <div class="col-md-3">
                    <label class="col-form-label">Account Head</label>
                    <div class="form-group">
                        <select name="account_id[${rowIndex}]" class="form-control select2" data-placeholder="Select Account">
                            <option></option>
                            @foreach($accounts as $row)
                <option value="{{ $row->id }}">
                                    {{ $row->name . ' ('. $row->accountType->name.')' }}
                </option>
@endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <label class="col-form-label">Debit</label>
            <div class="form-group">
                <input type="number" class="form-control debit-input" name="debit_amount[${rowIndex}]" value="0">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="col-form-label">Credit</label>
                    <div class="form-group">
                        <input type="number" class="form-control credit-input" name="credit_amount[${rowIndex}]" value="0">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="col-form-label">Journal Entry No</label>
                    <div class="form-group">
                        <input type="text" name="reference[${rowIndex}]" class="form-control" placeholder="Reference">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="col-form-label">Description</label>
                    <div class="form-group">
                        <input type="text" name="description[${rowIndex}]" class="form-control" placeholder="Description">
                    </div>
                </div>

                <div class="col-md-1">
                    <label class="col-form-label">&nbsp;</label>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                    </div>
                </div>
            </div>`;

                $("#transaction-rows").append(newRow);
                rowIndex++; // Increase row index for next row

                // Reinitialize Select2 for newly added dropdowns
                $(".select2").select2();

                // Attach event listener to new inputs
                attachSumCalculation();
            });

            // Function to remove a row
            $(document).on("click", ".remove-row", function () {
                $(this).closest(".transaction-row").remove();
                calculateSum(); // Recalculate totals when a row is removed
            });

            // Attach event listeners for sum calculation
            function attachSumCalculation() {
                $(".debit-input, .credit-input").off("input").on("input", function () {
                    calculateSum();
                });
            }

            // Function to calculate the sum of debit and credit fields
            function calculateSum() {
                let totalDebit = 0;
                let totalCredit = 0;

                $(".debit-input").each(function () {
                    let debitValue = parseFloat($(this).val()) || 0;
                    totalDebit += debitValue;
                });

                $(".credit-input").each(function () {
                    let creditValue = parseFloat($(this).val()) || 0;
                    totalCredit += creditValue;
                });

                $("#total-debit").text(totalDebit.toFixed(2));
                $("#total-credit").text(totalCredit.toFixed(2));
            }

            // Prevent form submission if debit ≠ credit
            $("#transaction-form").submit(function (event) {
                let totalDebit = parseFloat($("#total-debit").text()) || 0;
                let totalCredit = parseFloat($("#total-credit").text()) || 0;

                if (totalDebit !== totalCredit) {
                    event.preventDefault();
                    alert("Error: Total Debit and Total Credit must be equal!");
                }
            });


            // Initial binding for existing rows
            attachSumCalculation();
        });
    </script>


@endpush
