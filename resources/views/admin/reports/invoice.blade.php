@extends('admin.layouts.app')
@push('style')
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 10px;
                line-height: 1.2;
            }

            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .invoice {
                width: 100%;
                margin: 0;
                padding: 5px 0;
                page-break-inside: avoid; /* Prevent page break inside an invoice */
            }

            .card-body {
                margin-bottom: 0;
            }

            .card-header, .card-body {
                padding: 5px;
            }

            .invoice table, .invoice td, .invoice th {
                font-size: 10px;
                padding: 5px;
            }

            /* Force the content to fit within one page */
            @page {
                size: A4;
                margin: 5mm; /* Set minimal margins */
            }

            /* Remove unnecessary spacing to avoid empty second page */
            .card-header {
                page-break-before: avoid;
            }

            /* Remove any unnecessary page breaks after the last element */
            .invoice:last-child {
                page-break-after: auto;
            }

            /* This will force everything to fit in a single column */
            .container {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: flex-start;
            }
        }


    </style>
@endpush
@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{$title}}</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <!-- Form validation -->
        <div class="card">
            <!-- Product form -->
            <form action="{{ route('get-invoice-report') }}" class="flex-fill form-validate-jquery">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">


                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select Customer <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Customer"
                                                    name="customer_id" id="customer_id"
                                                    class="form-control select2 customer"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($customers as $key => $row)
                                                    <option
                                                        {{ request('customer_id') == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name . ' ' . $row->profile->mobile_no }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('customer_id'))
                                                <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Province -->
                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select Loan Application <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Loan Application"
                                                    name="application_id" id="application_id"
                                                    data-type="application"
                                                    class="form-control select2 application"
                                                    data-fouc>
                                                <option></option>

                                            </select>
                                            @if ($errors->has('application_id'))
                                                <span class="text-danger">{{ $errors->first('application_id') }}</span>
                                            @endif
                                        </div>
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-outline-primary float-end">Get Invoice
                                        </button>
                                        <a href="{{ route('show-invoice-report') }}"
                                           class="btn btn-outline-dark me-3 float-end">Reset</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /product form -->
        </div>
        <!-- /form validation -->

        @if(isset($invoiceData))
            <div class="row justify-content-center mt-3">
                <div class="col-auto">
                    <button id="printBtn" type="button"
                            class="btn btn-info btn-labeled btn-labeled-left check-total">
                        <b><i class="icon-printer"></i></b> Print
                    </button>
                </div>
                <div class="col-auto">
                    {{--                    <button id="pdfBtn" type="button"--}}
                    {{--                            data-invoice="{{ $invoiceData['borrower_name'] . '-INV-' .  $invoiceData['loan_account_no'] }}"--}}
                    {{--                            class="btn btn-warning btn-labeled btn-labeled-left check-total">--}}
                    {{--                        <b><i class="icon-download"></i></b> PDF--}}
                    {{--                    </button>--}}
                    <a href="{{ route('invoice.download', ['customer_id' => request('customer_id'), 'application_id' => request('application_id')]) }}"
                       class="btn btn-danger">
                        Download PDF
                    </a>


                </div>
            </div>

            <div class="invoice mt-5" id="printInvoice">
                <div class="card shadow-lg">
                    <!-- Header Section -->
                    <div class="card-header text-center bg-info text-white py-4">
                        <div class="row">
                            <div class="col-md-6 text-start">
                                <img width="150" src="{{ asset('backend/img/icons/logo.jpg') }}" alt="Sarmaya Logo">
                            </div>
                            <div class="col-md-6 text-end">
                                <h2 class="mb-0 text-white">Sarmaya Microfinance (Private) Limited</h2>
                                <h4 class="text-white">{{ $title }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Body Section -->
                    <div class="card-body px-4">
                        <!-- Invoice Details -->
                        <h3 class="border-bottom pb-2">Invoice Details</h3>
                        <table class="table table-bordered mb-4">
                            <tbody>
                            <tr>
                                <th>Invoice Number</th>
                                <td>{{ 'INV-' . now()->timestamp }}</td>
                                <th>Date</th>
                                <td>{{ now()->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <td>{{ $invoiceData['borrower_name'] }}</td>
                                <th>CNIC</th>
                                <td>{{ $invoiceData['cnic'] }}</td>
                            </tr>
                            <tr>
                                <th>Mobile Number</th>
                                <td>{{ $invoiceData['mobile_no'] }}</td>
                                <th>Loan Account Number</th>
                                <td>{{ $invoiceData['loan_account_no'] }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Loan Details -->
                        <h3 class="border-bottom pb-2">Loan Details</h3>
                        <table class="table table-bordered mb-4">
                            <tbody>
                            <tr>
                                <th>Total Loan Amount</th>
                                <td>{{ number_format($invoiceData['loan_amount'], 2) }}</td>
                            </tr>
                            {{--                            <tr>--}}
                            {{--                                <th>Processing Fee ({{ $invoiceData['processing_fee_percentage'] }}%)</th>--}}
                            {{--                                <td>{{ number_format($invoiceData['processing_fee'], 2) }}</td>--}}
                            {{--                            </tr>--}}
                            {{--                            <tr>--}}
                            {{--                                <th>Total Interest</th>--}}
                            {{--                                <td>{{ number_format($invoiceData['total_interest'], 2) }}</td>--}}
                            {{--                            </tr>--}}
                            <tr>
                                <th>Total Payable Amount</th>
                                <td>{{ number_format($invoiceData['total_payable'], 2) }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Payment Details -->
                        <h3 class="border-bottom pb-2">Payment Details</h3>
                        <table class="table mb-4">
                            <thead>
                            <tr class="bg-info text-white">
                                <th>#</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Amount Due (PKR)</th>
                                <th>Amount Paid (PKR)</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $isFirstInstallment = true; @endphp
                            @php $totalPaid = 0; $totalDue = 0; @endphp
                            @foreach($invoiceData['installments'] as $index => $detail)
                                @php
                                    $totalPaid += $detail->amount_paid;
                                    $totalDue += $detail->amount_due - $detail->amount_paid;
                                @endphp
                                <tr>
                                    <td>{{ $detail->installment_number }}</td>
                                    @if($isFirstInstallment)
                                        <td>{{ showDate($detail->issue_date) }}</td>

                                        @php $isFirstInstallment = false; @endphp
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ showDate($detail->due_date) }}</td>
                                    <td>{{ number_format($detail->amount_due, 2) }}</td>
                                    <td>{{ number_format($detail->amount_paid, 2) }}</td>
                                    <td>{{ $detail->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                        <h3  class="border-bottom pb-2">Recovery Details</h3>

                        <table class="table mb-4">
                            <thead>
                            <tr class="bg-info text-white">
                                <th>Installment</th>
                                <th>Installment Amount</th>
{{--                                <th>OverDue Days (PKR{{ env('LATE_FEE') }}/day)</th>--}}
{{--                                <th>Late Fee</th>--}}
{{--                                <th>Waive Off Charges</th>--}}
                                <th>Total Amount</th>
{{--                                <th>Payment Method</th>--}}
{{--                                <th>Status</th>--}}
{{--                                <th>Remarks</th>--}}
                                <th>Date</th>

                            </tr>
                            </thead>
                            <tbody>
                            @if(count($invoiceData['recoveries']) > 0)
                                @foreach($invoiceData['recoveries'] as $recovery)
                                    <tr>
                                        <td>{{ $recovery->installmentDetail->installment_number }}</td>
                                        <td>{{ $recovery->amount }}</td>
{{--                                        <td>{{ $recovery->overdue_days ?? 'N/A' }}</td>--}}
{{--                                        <td>{{ $recovery->penalty_fee ?? 'N/A' }}</td>--}}
{{--                                        <td>{{ $recovery->waive_off_charges ?? '0' }}</td>--}}
                                        <td>{{ ucfirst($recovery->total_amount) }}</td>
{{--                                        <td>{{ ucfirst($recovery->payment_method) }}</td>--}}
{{--                                        <td>{{ ucfirst($recovery->status) }}</td>--}}
{{--                                        <td>--}}
{{--                                            {{ $recovery->remarks }}--}}
{{--                                            @if($recovery->is_early_settlement)--}}
{{--                                                <br>--}}
{{--                                                <b class="text-danger">--}}
{{--                                                    {{ ($recovery->percentage) }}%--}}
{{--                                                    of {{ ($recovery->remaining_amount) }}--}}
{{--                                                    is {{ ($recovery->erc_amount) }}--}}
{{--                                                </b><br>--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
                                        <td>{{ showDate($recovery->recovery_date) }}</td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center fw-bold">No Record Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>


                        <!-- Summary -->
                        <h3 class="border-bottom pb-2">Summary</h3>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th>Total Paid</th>
                                <td>{{ number_format($totalPaid, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Outstanding Amount</th>
                                <td>{{ number_format($totalDue, 2) }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Footer Notes -->
                        <div class="bg-danger-light  p-4 mt-4">
                            <strong>Note:</strong> Please ensure timely payments to maintain a good credit record with
                            Sarmaya Microfinance. A penalty of PKR 200 per day will be applied for delayed payments as
                            per the loan agreement.
                            <br>For any discrepancies or questions regarding this invoice, contact us at <a
                                href="mailto:support@sarmayamf.com">support@sarmayamf.com</a>.
                            <br><em>ہم آپ کے اعتماد کی قدر کرتے ہیں اور مستقبل میں آپ کی خدمت کے منتظر ہیں۔</em>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>
    <!-- /content area -->
@endsection

@push('script')
    <script src="{{asset('backend/custom/js/jspdf.umd.min.js')}}"></script>
    <script src="{{asset('backend/custom/js/html2canvas.min.js')}}"></script>
    <script src="{{asset('backend/custom/js/html2canvas.js')}}"></script>
    <script src="{{asset('backend/custom/js/printThis.js')}}"></script>
    <script>
        $(document).ready(function () {
            window.html2canvas = html2canvas; // add this line of code
            window.jsPDF = window.jspdf.jsPDF; // add this line of code

            $('#printBtn').on('click', function () {
                var invoices = $("#printInvoice"); // Select the invoice element
                var container = $('<div></div>'); // Create a container element to hold all invoices

                // Append the HTML content of all invoices into the container
                invoices.each(function (index, invoice) {
                    var invoiceHTML = $(invoice).html(); // Get the HTML content of each invoice
                    container.append('<div class="invoice">' + invoiceHTML + '</div>'); // Append invoice HTML to the container
                });

                // Print the container with all invoices
                container.printThis({
                    importCSS: true, // Import CSS for printing
                    loadCSS: "", // Path to external CSS file (if needed)
                    header: null, // Exclude header from the printed output
                    footer: null, // Exclude footer from the printed output
                    pageTitle: "All Invoices", // Set a custom page title
                    printDelay: 500, // Adjust delay if needed for large content
                    pageSize: 'A4', // Force the page size to A4
                    canvas: true, // Render canvas content if necessary
                    afterPrint: function () {
                        console.log("All invoices printed");
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });

    </script>
@endpush
