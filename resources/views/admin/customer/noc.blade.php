@extends('admin.layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .company-logo {
            width: 500px;
            margin: 20px 0;
        }

        .agreement-content {
            margin-top: 30px;
        }

        .signature-section {
            margin-top: 50px;
        }
    </style>
@endpush
@section('content')
    <div class="row justify-content-center mt-3">
        <div class="col-auto">
            <button id="printBtn" type="button"
                    class="btn btn-info btn-labeled btn-labeled-left check-total">
                <b><i class="icon-printer"></i></b> Print
            </button>
        </div>
        <div class="col-auto">

        </div>
    </div>

    <div class="container">
        <div class="invoice mt-5" id="printDiv">

            <div class="card shadow-lg p-4">
                <!-- Company Logo -->
                <div class="text-center">
                    <img width="500" src="{{ asset('backend/img/icons/logo.jpg') }}" alt="Company Logo"
                         class="company-logo">
                </div>


                    <h3 class="text-center text-uppercase">No Objection Certificate (NOC)</h3>
                    <hr>
                    <p class="text-end">Date: <strong>{{ now()->format('d/m/Y') }}</strong></p>
                    <p class="text-end">NOC No: <strong>SMPL/NOC/{{$loanApplication->application_id}}</strong></p>
                    <h5 class="text-uppercase">To Whom It May Concern</h5>
                    <p>This is to certify that <strong>{{$customer->name}}</strong>, CNIC No.<strong>{{$customer->profile->cnic_no}}</strong>,
                        resident of <strong>{{$customer->profile->permanent_address}},
                            {{$customer->city->name}},
                            {{$customer->district->name}},{{$customer->province->name}}
                        </strong>, has successfully repaid the entire loan amount availed from <strong>Sarmaya Microfinance (Private) Limited</strong> under Loan Account ID <strong>{{$loanApplication->application_id}}</strong>.</p>
                    <p>The borrower has cleared all outstanding dues, including the principal amount, interest, and any applicable charges as per the loan agreement. As of the issuance date of this certificate, there are no pending liabilities against the borrower in our records.</p>
                    <p>Therefore, <strong>Sarmaya Microfinance (Private) Limited</strong> has no objection to the borrower’s financial dealings and grants this NOC for their future financial and banking purposes.</p>
                    <p>This certificate is issued upon the borrower’s request and holds no further legal or financial obligation for <strong>Sarmaya Microfinance (Private) Limited</strong>.</p>
                    <p>For any verification or queries, please contact our office at <strong>+92 323 5420352</strong>.</p>
                    <br>
                    <p class="fw-bold">Authorized Signatory</p>
                    <p>Ms. Amrah Rubab</p>
                    <p>Manager Finance</p>
                    <p>Sarmaya Microfinance (Private) Limited</p>
                    <hr>
                    <p class="text-muted text-center">“This is an electronically signed system-generated document and does not require a physical signature. Contact HR for document verification.”</p>
            </div>
        </div>
    </div>

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
                var invoices = $("#printDiv"); // Select the invoice element
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

@endpush
