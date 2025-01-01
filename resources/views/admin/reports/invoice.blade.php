@extends('admin.layouts.app')
@push('style')

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
                    <button id="pdfBtn" type="button"
                            data-invoice="{{ $invoiceData['borrower_name'] . '-INV-' .  $invoiceData['loan_account_no'] }}"
                            class="btn btn-warning btn-labeled btn-labeled-left check-total">
                        <b><i class="icon-download"></i></b> PDF
                    </button>
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
                            @php $totalPaid = 0; $totalDue = 0; @endphp
                            @foreach($invoiceData['installments'] as $index => $detail)
                                @php
                                    $totalPaid += $detail->amount_paid;
                                    $totalDue += $detail->amount_due - $detail->amount_paid;
                                @endphp
                                <tr>
                                    <td>{{ $detail->installment_number }}</td>
                                    <td>{{ showDate($detail->issue_date) }}</td>
                                    <td>{{ showDate($detail->due_date) }}</td>
                                    <td>{{ number_format($detail->amount_due, 2) }}</td>
                                    <td>{{ number_format($detail->amount_paid, 2) }}</td>
                                    <td>{{ $detail->status }}</td>
                                </tr>
                            @endforeach
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
                            <strong>Note:</strong> Please ensure timely payments to maintain a good credit record with Sarmaya Microfinance. A penalty of PKR 200 per day will be applied for delayed payments as per the loan agreement.
                            <br>For any discrepancies or questions regarding this invoice, contact us at <a href="mailto:support@sarmayamf.com">support@sarmayamf.com</a>.
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
            $(function () {

                $('#printBtn').on('click', function () {
                    var invoices = $("#printInvoice"); // Select all elements with class "print-bill"
                    var container = $('<div></div>'); // Create a container element to hold all invoices

                    // Loop through each invoice and append its HTML content to the container
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
                        afterPrint: function () {
                            // Callback function after printing (optional)
                            console.log("All invoices printed");
                        }
                    });
                });


                $('#pdfBtn').on('click', function () {
                    var input = document.getElementById("printInvoice");
                    const invoiceNumber = $(this).data('invoice');
                    html2canvas(input)
                        .then((canvas) => {
                            const imgData = canvas.toDataURL('image/png');
                            // var pdf = new jsPDF("p", "mm", "a4");
                            var pdf = new jsPDF("p", "in", "legal"); // Set page orientation to landscape ("l") and page size to legal
                            const imgProps = pdf.getImageProperties(imgData);
                            const pdfWidth = pdf.internal.pageSize.getWidth();
                            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                            // Add extra height to accommodate content cut from the bottom
                            const extraHeight = -0.5; // Adjust this value as needed
                            const adjustedPdfHeight = pdfHeight + extraHeight;

                            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, adjustedPdfHeight);
                            pdf.save(invoiceNumber + '.pdf');
                        });
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
