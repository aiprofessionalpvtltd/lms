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

            <div class="card">

                <!-- Company Logo -->
                <div class="text-center">
                    <img width="500" src="{{ asset('backend/img/icons/logo.jpg') }}" alt="Company Logo"
                         class="company-logo">
                </div>

                <div class="card-body px-4">
                <!-- Loan Agreement -->
                <div class="agreement-content">
                    <h2 class="text-center mb-4 float-end">
                        <u><strong> {{ $loanApplication->application_id ?? 'N/A'}}</strong></u></h2>
                    <br>
                    <h2 class="text-center fw-bold mb-4 mt-4">Loan Agreement</h2>
                    <p>This Loan Agreement (the "Agreement") is entered into on this {{ currentDate() }}, by
                        and between:</p>

                    <p><strong>{{env('COMPANY_NAME')}}</strong>, a company duly registered under the laws of Pakistan,
                        with its
                        registered office
                        at <strong>{{env('COMPANY_ADDRESS')}}</strong>, hereinafter referred to as the "Lender,"</p>
                    <p>AND</p>
                    <p><strong>{{$customer->profile->first_name .' ' . $customer->profile->last_name}}</strong>,
                        residing at {{$customer->profile->permanent_address}}, hereinafter referred to as the
                        "Borrower,"</p>
                    <p>AND</p>
                    <p><strong>{{$customer->references[0]->guarantor_contact_name ?? ''}}</strong>, residing
                        at {{$customer->references[0]->guarantor_contact_number ?? ''}},
                        <strong>{{$customer->references[1]->guarantor_contact_name ?? ''}}</strong>, residing
                        at {{$customer->references[1]->guarantor_contact_number ?? ''}},
                        hereinafter referred to as the "Guarantor."</p>
                    <p>Both the Lender, the Borrower, and the Guarantor may be referred to individually as a "Party" and
                        collectively as the "Parties."</p>

                    <h4>1. Loan Amount and Disbursement</h4>
                    <p>1.1. The Lender agrees to loan the Borrower an amount of PKR
                        <strong>{{ $loanApplicationProduct->loan_amount }}</strong> (the "Loan Amount").</p>
                    <p>1.2. The Loan Amount will be disbursed to the Borrower's
                        <strong>{{ $customer->bank_account->account_number }}</strong> or through the mobile wallet
                        [JazzCash/Easypaisa/Zindagi] after signing this Agreement.</p>
                    <p>1.3. A Handling fee of <strong>{{ $loanApplicationProduct->processing_fee_percentage }}%</strong>
                        of the
                        Loan Amount will be deducted at the time of disbursement.</p>
                    <p>1.4. The Borrower will provide post-dated cheques for the full Loan Amount and scheduled
                        installments at
                        the time of signing this Agreement...</p>

                    <h4>2. Interest Rate and Payment Terms</h4>
                    <p>2.1. The Loan Amount shall accrue interest on the outstanding principal balance at a rate
                        proportional to
                        the agreed loan term, calculated on a pro-rata basis relative to the duration of the loan period
                        selected by the Borrower.</p>
                    <p>2.2. The Loan Amount, including interest, shall be repaid in
                        <strong>{{ $loanApplicationProduct->loanDuration->value }}</strong> equal installments
                        of PKR <strong>{{ $loanApplicationProduct->monthly_installment_amount }}</strong>,
                        due on the
                        <strong>{{ formatOrdinal(date('d',strtotime($loanApplicationFirstInstallments->issue_date)))}}</strong>
                        of each month.</p>
                    <p>2.3. Late payments shall incur a penalty fee of PKR <strong>{{env('LATE_FEE')}}</strong> per day,
                        calculated from the day after the
                        installment due date.</p>
                    <p>2.4. In case the Borrower fails to make payments for 90 days, the Lender has the right to present
                        the
                        post-dated cheque(s) for payment. Any dishonored cheque shall result in additional
                        penalties..</p>


                    <h4>3. Repayment Obligations</h4>
                    <p>3.1. The Borrower agrees to repay the Loan Amount in full, along with applicable interest, as per
                        the
                        agreed repayment schedule.</p>
                    <p>3.2. Loan repayments will be made via one of the following methods:
                    <p>(a) Direct Bank Transfer to the Lender's account:
                    <p><strong>Bank Name:</strong> {{ $customer->bank_account->bank_name }}</p>
                    <p><strong>Account Title:</strong> {{ $customer->bank_account->account_name }}</p>
                    <p><strong>Account Number:</strong> {{ $customer->bank_account->account_number }}</p>
                    <p><strong>IBAN:</strong> {{ $customer->bank_account->iban }}</p>
                    </p>
                    <p> (b) Mobile wallet payment via JazzCash/Easypaisa or any other approved payment gateway
                        integrated into
                        the mobile app.</p>
                    <p> (c) Auto-debit from the Borrower’s account, provided this feature is activated, or</p>
                    <p> (d) Presentation of post-dated cheques in case of non-payment.</p>
                    </p>

                    <p>3.3. In the event of non-repayment or default, the Lender reserves the right to:
                    <p>(a) Hold the Guarantor fully liable for the outstanding amount, and</p>
                    <p> (b) Take legal action against both the Borrower and Guarantor for the recovery of dues.</p>

                    </p>


                    <h4>4. Guarantor’s Responsibilities</h4>
                    <p>4.1. The Guarantor guarantees the full and timely repayment of the Loan Amount, including
                        interest and
                        penalties, if the Borrower defaults or refuses to make payment.</p>
                    <p>4.2. In the event of default by the Borrower, the Guarantor will be held liable to repay the Loan
                        Amount
                        immediately.</p>
                    <p>4.3. The Lender may take legal action against the Guarantor if the Borrower fails to repay, and
                        the
                        Guarantor fails to fulfill their obligations under this Agreement.</p>

                    <h4>5. Late Payment Charges</h4>
                    <p>5.1. In the event that the Borrower fails to repay any installment by the due date, a late
                        payment
                        charge of <strong>PKR {{env('LATE_FEE')}} per day</strong> will apply until the installment is
                        paid in
                        full.</p>
                    <p>5.2. If the Borrower does not make the overdue payment within <strong>90 days</strong>, the
                        Lender
                        reserves the right to:
                    <p>(a) Present the post-dated cheque(s) provided by the Borrower.</p>
                    <p> (b) Enforce collection through the Guarantor.</p>
                    <p> (c) Report the Borrower to the credit bureau, which will negatively impact their credit
                        history.</p>

                    </p>
                    <p>5.3. The Borrower shall bear all legal and collection costs incurred by the Lender in enforcing
                        this
                        Agreement, including recovery fees and litigation expense.</p>

                    <h4>6. Post-Dated Cheques</h4>
                    <p>6.1. The Borrower agrees to issue post-dated cheques in favor of the Lender for the full Loan
                        Amount and each monthly installment as security for timely repayments.</p>
                    <p>6.2. Should any cheque be dishonored, the Borrower will be liable for legal penalties under the
                        <strong>Negotiable Instruments Act, 1881,</strong> and all costs associated with the recovery of
                        the Loan Amount.</p>
                    <p>6.3. Dishonoring a cheque will result in the immediate acceleration of all remaining payments,
                        making the full outstanding Loan Amount immediately due and payable.</p>


                    <h4>7. Default and Legal Recourse</h4>
                    <p>7.1. The following will constitute a default under this Agreement:</p>
                    <p>(a) Failure to make any payment by the due date.</p>
                    <p>(b) The dishonor of any post-dated cheque provided by the Borrower.</p>
                    <p>(c) Breach of any term of this Agreement by the Borrower.</p>
                    <p>7.2. In the event of default, the Lender may:</p>
                    <p>(a) Accelerate the Loan, making the full outstanding Loan Amount and all accrued interest
                        immediately due and payable.</p>
                    <p>(b) Initiate legal proceedings to recover the outstanding Loan Amount from the Borrower and/or
                        the Guarantor.</p>
                    <p>(c) Enforce the post-dated cheques for immediate payment.</p>
                    <p>(d) Report the Borrower and Guarantor to relevant authorities, credit bureaus, and other
                        institutions, which may result in legal consequences and a damaged credit rating.</p>
                    <p>7.3. The Guarantor will be fully liable for the Loan Amount in case of the Borrower's default,
                        and the Lender may recover dues directly from the Guarantor without first exhausting remedies
                        against the Borrower.</p>

                    <h4>8. Prepayment</h4>
                    <p>8.1. The Borrower may prepay the outstanding Loan Amount in full or in part at any time.</p>
                    <p>8.2. Prepayments will not incur any penalties; however, interest will be calculated up to the
                        date of prepayment.</p>

                    <h4>9. Use of Loan</h4>
                    <p>9.1. The Borrower agrees to use the Loan Amount exclusively for the following purpose(s):
                        <strong>{{ $loanApplication->loanPurpose->name }}</strong>.</p>
                    <p>9.2. Any misuse of the Loan Amount for purposes not agreed upon will constitute a breach of this
                        Agreement and may lead to immediate termination of the loan facility.</p>

                    <h4>10. Confidentiality and Data Protection</h4>
                    <p>10.1. The Lender agrees to maintain the confidentiality of the Borrower’s and Guarantor’s
                        personal and financial information in compliance with applicable data protection laws.</p>
                    <p>10.2. The Borrower and Guarantor consent to the Lender's use of their data for processing the
                        loan, payment tracking, credit evaluation, and reporting purposes.</p>
                    <p>10.3. The Lender may share the Borrower’s and Guarantor’s data with third parties for credit
                        evaluation, debt collection, or legal purposes if necessary.</p>

                    <h4>11. Amendments</h4>
                    <p>11.1. This Agreement may be amended only by mutual written consent of both Parties.</p>
                    <p>11.2. Any modifications to the terms of this Agreement will be communicated to the Borrower via
                        email or through the mobile app.</p>

                    <h4>12. Termination</h4>
                    <p>12.1. This Agreement shall terminate upon full repayment of the Loan Amount, including interest
                        and any additional fees, by the Borrower or the Guarantor.</p>
                    <p>12.2. The Lender reserves the right to terminate this Agreement immediately upon the Borrower's
                        default or breach of any terms, as well as to enforce legal proceedings against the Borrower
                        and/or Guarantor.</p>

                    <h4>13. Governing Law and Dispute Resolution</h4>
                    <p>13.1. This Agreement shall be governed by and construed in accordance with the laws of
                        Pakistan.</p>
                    <p>13.2. In case of any disputes arising out of or in connection with this Agreement,
                        the Parties shall first attempt to resolve the dispute amicably.
                        If the dispute is not resolved, it shall be referred to arbitration in accordance with the
                        <strong>Arbitration Act, 1940,</strong> of Pakistan.</p>

                    <h4>14. Miscellaneous</h4>
                    <p>14.1. The Borrower and Guarantor agree to receive communications, including loan notifications
                        and payment reminders, through the mobile app and email.</p>
                    <p>14.2. If any provision of this Agreement is found invalid or unenforceable by a court of law, the
                        remaining provisions shall continue in full force and effect.</p>


                    <h4 class="signature-section">15. Acceptance</h4>
                    <p>By signing this Agreement digitally via the mobile application, the Borrower and Guarantor
                        acknowledge
                        that they have read, understood, and agreed to the terms and conditions of this.</p>

                    <!-- Signatures -->
                    <div class="row signature-section">
                        <div class="col-6">
                            <p><strong>Lender</strong></p>
                            <p><strong>{{env('COMPANY_NAME')}}</strong></p>
                            <p>Signature: ___________________</p>
                            <p>Name: {{auth()->user()->name}}</p>
                            <p>Date: {{currentDate()}}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Borrower</strong></p>
                            <p><strong>{{$customer->profile->first_name .' ' . $customer->profile->last_name}}</strong>
                            </p>
                            <p>Signature: ___________________</p>
                            <p>Date: {{currentDate()}}</p>
                        </div>
                    </div>
                    <div class="row signature-section">
                        <div class="col-6">
                            <p><strong>Guarantor 1</strong></p>
                            <p><strong>{{$customer->references[0]->guarantor_contact_name ?? ''}}</strong></p>
                            <p><strong>{{$customer->references[0]->guarantor_contact_number ?? ''}}</strong></p>
                            <p>Signature: ___________________</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Guarantor 2</strong></p>
                            <p><strong>{{$customer->references[1]->guarantor_contact_name ?? ''}}</strong></p>
                            <p><strong>{{$customer->references[1]->guarantor_contact_number ?? ''}}</strong></p>
                            <p>Signature: ___________________</p>
                        </div>
                    </div>

                    <!-- Approval Section -->
                    <div class="approval-section mt-5">
                        <p><strong>Approved by:</strong></p>
                        <p>Date of Approval: ___________________</p>
                    </div>
                </div>
                </div>
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
