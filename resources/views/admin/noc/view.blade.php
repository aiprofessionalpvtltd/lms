@extends('admin.layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

@endpush
@section('content')
    <!--**********************************
            Content body start
        ***********************************-->

    <!-- Content area -->
    <div class="content">
        <div class="card mb-4">

            <div class="card shadow-lg p-4">
                <h3 class="text-center text-uppercase">No Objection Certificate (NOC)</h3>
                <hr>
                <p class="text-end">Date: <strong>[DD/MM/YYYY]</strong></p>
                <p class="text-end">NOC No: <strong>[XXXXXX]</strong></p>
                <h5 class="text-uppercase">To Whom It May Concern</h5>
                <p>This is to certify that <strong>[Borrower’s Name]</strong>, CNIC No. <strong>[XXXXX-XXXXXXX-X]</strong>, resident of <strong>[Full Address]</strong>, has successfully repaid the entire loan amount availed from <strong>Sarmaya Microfinance (Private) Limited</strong> under Loan Account ID <strong>[XXXXXXXX]</strong>.</p>
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
        <a href="{{ route('show-customer') }}" class="btn btn-primary">Back to Customers List</a>

    </div>
    <!-- /content area -->
    <!--**********************************
        Content body end
    ***********************************-->

@endsection

@push('script')




@endpush
