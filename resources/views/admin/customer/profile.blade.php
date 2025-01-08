@extends('admin.layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
@endpush

@section('content')
    <div class="content">
        <div class="row justify-content-center">
            <div class="col-auto">
                <button id="printBtn" type="button"
                        class="btn btn-info btn-labeled btn-labeled-left check-total">
                    <b><i class="icon-printer"></i></b> Print
                </button>
            </div>
            <div class="col-auto">

            </div>
        </div>

        <div class="invoice mt-5" id="printDiv">

            <div class="card mb-4">
                <!-- Company Logo -->
                <div class="text-center">
                    <img width="500" src="{{ asset('backend/img/icons/logo.jpg') }}" alt="Company Logo"
                         class="company-logo">
                </div>

                <div class="card-body">
                    <!-- User Information Table -->
                    <h2>User Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>First Name</th>
                            <td>{{ $customer->profile->first_name }}</td>
                        </tr>
                        <tr>
                            <th>Last Name</th>
                            <td>{{ $customer->profile->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>{{ $customer->profile->gender->name }}</td>
                        </tr>
                        <tr>
                            <th>Nationality</th>
                            <td>{{ $customer->profile->nationality->name }}</td>
                        </tr>
                        <tr>
                            <th>Province</th>
                            <td>{{ $customer->province->name }}</td>
                        </tr>
                        <tr>
                            <th>District</th>
                            <td>{{ $customer->district->name }}</td>
                        </tr>
                        <tr>
                            <th>City</th>
                            <td>{{ $customer->city->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>CNIC No</th>
                            <td>{{ $customer->profile->cnic_no }}</td>
                        </tr>
                        <tr>
                            <th>Issue Date</th>
                            <td>{{ showDate($customer->profile->issue_date) }}</td>
                        </tr>
                        <tr>
                            <th>Expire Date</th>
                            <td>{{ showDate($customer->profile->expire_date) }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{ showDate($customer->profile->dob) }}</td>
                        </tr>
                        <tr>
                            <th>Mobile No</th>
                            <td>{{ $customer->profile->mobile_no }}</td>
                        </tr>
                        <tr>
                            <th>Alternate Mobile No</th>
                            <td>{{ $customer->profile->alternate_mobile_no }}</td>
                        </tr>
                        <tr>
                            <th>Permanent Address</th>
                            <td>{{ $customer->profile->permanent_address }}</td>
                        </tr>
                        <tr>
                            <th>Current Address</th>
                            <td>{{ $customer->profile->current_address }}</td>
                        </tr>
                        <tr>
                            <th>Current Address Duration</th>
                            <td>{{ $customer->profile->residenceDuration->name }}</td>
                        </tr>
                        <tr>
                            <th>Current Residence</th>
                            <td>{{ $customer->profile->residenceType->name }}</td>
                        </tr>
                    </table>
                    <!-- Profile Documents Table -->
                    <h2>Profile Documents</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Profile Photo</th>
                            <td>
                                <a href="{{ asset('storage/' . $customer->profile->photo) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $customer->profile->photo) }}" width="50"
                                         height="50"
                                         alt="Profile Photo" class="img-thumbnail" style="max-width: 150px;">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>CNIC Front</th>
                            <td>
                                <a href="{{ asset('storage/' . $customer->profile->cnic_front) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $customer->profile->cnic_front) }}" width="50"
                                         height="50" alt="CNIC Front" class="img-thumbnail" style="max-width: 150px;">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>CNIC Back</th>
                            <td>
                                <a href="{{ asset('storage/' . $customer->profile->cnic_back) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $customer->profile->cnic_back) }}" width="50"
                                         height="50" alt="CNIC Back" class="img-thumbnail" style="max-width: 150px;">
                                </a>
                            </td>
                        </tr>
                    </table>
                    <!-- Bank Information Table -->
                    <h2>Bank Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Bank Name</th>
                            <td>{{ $customer->bank_account->bank_name }}</td>
                        </tr>
                        <tr>
                            <th>Account Title</th>
                            <td>{{ $customer->bank_account->account_name }}</td>
                        </tr>
                        <tr>
                            <th>Account Number</th>
                            <td>{{ $customer->bank_account->account_number }}</td>
                        </tr>
                        <tr>
                            <th>IBAN</th>
                            <td>{{ $customer->bank_account->iban }}</td>
                        </tr>
                        <tr>
                            <th>Swift Code</th>
                            <td>{{ $customer->bank_account->swift_code }}</td>
                        </tr>
                    </table>

                    <!-- Family and Dependents Information Table -->
                    <h2>Family and Dependents Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>No of Dependents</th>
                            <td>{{ $customer->familyDependent->number_of_dependents }}</td>
                        </tr>
                        <tr>
                            <th>Spouse Name</th>
                            <td>{{ $customer->familyDependent->spouse_name }}</td>
                        </tr>
                        <tr>
                            <th>Spouse Employment Detail</th>
                            <td>{{ $customer->familyDependent->spouse_employment_details }}</td>
                        </tr>
                    </table>

                    <!-- Employment & Financial Information Table -->
                    <h2>Employment & Financial Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Employment Status</th>
                            <td>{{ $customer->employment->employmentStatus->status }}</td>
                        </tr>
                        <tr>
                            <th>Income Source</th>
                            <td>{{ $customer->employment->incomeSource->source }}</td>
                        </tr>
                        <tr>
                            <th>Employer/Business Name</th>
                            <td>{{ $customer->employment->current_employer }}</td>
                        </tr>
                        <tr>
                            <th>Position/Role</th>
                            <td>{{ $customer->employment->job_title->name }}</td>
                        </tr>
                        <tr>
                            <th>Years of Employment/Business</th>
                            <td>{{ $customer->employment->employment_duration }}</td>
                        </tr>
                        <tr>
                            <th>Monthly Income</th>
                            <td>{{ $customer->employment->gross_income }}</td>
                        </tr>
                        <tr>
                            <th>Net Income</th>
                            <td>{{ $customer->employment->net_income }}</td>
                        </tr>
                        <tr>
                            <th>Existing Loan</th>
                            <td>{{ $customer->employment->existingLoan->name }}</td>
                        </tr>
                    </table>

                    <!-- Guarantors Contact Information Table -->
                    <h2>Guarantors Contact Information</h2>
                    <table class="table table-bordered">
                        @foreach($customer->references as $key =>  $row)
                            <tr>
                                <th>Guarantor {{ $key+1 }}</th>
                                <td>
                                    <p><strong>Name:</strong> {{ $row->guarantor_contact_name }}</p>
                                    <p><strong>Relationship:</strong> {{ $row->relationship->name }}</p>
                                    <p><strong>Contact:</strong> {{ $row->guarantor_contact_number }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </table>



                    <h2>Loan Application</h2>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Loan ID</th>

                            <th>Loan Amount</th>
                            <th>Duration</th>
                            <th>Purpose</th>
                            <th>Completed</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($loanApplications as $loanApplication)
                            <tr>
                                <td>{{$loanApplication->application_id}}</td>

                                <td>{{$loanApplication->loan_amount}}</td>
                                <td>{{$loanApplication->loanDuration->name}}</td>
                                <td>{{$loanApplication->loanPurpose->name}}</td>
                                <td>{{showBoolean($loanApplication->is_completed)}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <h2>Installment Detail</h2>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Application Amount</th>
                            <th>Installment Duration</th>
                            <th>Total Payable Amount</th>
                            <th>Monthly Installment</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($installments as $installment)
                            <tr>
                                <td>{{ $installment->loanApplication->application_id ?? 'N/A' }}</td>
                                <td>{{ $installment->loanApplication->loan_amount ?? 'N/A' }}</td>
                                <td>{{ $installment->loanApplication->loanDuration->name ?? 'N/A' }}</td>
                                <td>{{ $installment->total_amount }}</td>
                                <td>{{ $installment->monthly_installment }}</td>


                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <h2>Installment detail</h2>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $isFirstInstallment = true; @endphp
                        @foreach($installments as $installment)
                            <tr>
                                <td colspan="6" class="text-center fw-bold">
                                    {{ $installment->loanApplication->application_id ?? 'N/A' }}
                                </td>
                            </tr>

                            @foreach($installment->details as $detail)
                                <tr data-id="{{ $detail->id }}">
                                    <td>{{ $detail->installment_number }}</td>

                                    @if($isFirstInstallment)
                                        <td>
                                            <span class="issue-date-text">{{ showDate($detail->issue_date) }}</span>
                                            <input type="date" class="issue-date-input d-none"
                                                   value="{{ $detail->issue_date }}"/>
                                        </td>
                                        @php $isFirstInstallment = false; @endphp
                                    @else
                                        <td></td>
                                    @endif

                                    <td>
                                        <span class="due-date-text">{{ showDate($detail->due_date) }}</span>
                                        <input type="date" class="due-date-input d-none"
                                               value="{{ $detail->due_date }}"/>
                                    </td>

                                    <td>{{ $detail->amount_due }}</td>
                                    <td>{{ $detail->amount_paid }}</td>
                                    <td>{{ strtoupper($detail->status) }}</td>


                                </tr>
                            @endforeach
                        @endforeach

                        </tbody>
                    </table>

                    <h2>Recovery Detail</h2>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Installment</th>
                            <th>Installment Amount</th>
                            <th>OverDue Days (PKR{{ env('LATE_FEE') }}/day)</th>
                            <th>Late Fee</th>
                            <th>Waive Off Charges</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($installments as $installment)
                            <tr>
                                <td colspan="10" class="text-center fw-bold">
                                    {{ $installment->loanApplication->application_id ?? 'N/A' }}
                                </td>
                            </tr>

                            @foreach($installment->recoveries as $recovery)
                                <tr>
                                    <td>{{ $recovery->installmentDetail->installment_number }}</td>
                                    <td>{{ $recovery->amount }}</td>
                                    <td>{{ $recovery->overdue_days ?? 'N/A' }}</td>
                                    <td>{{ $recovery->penalty_fee ?? 'N/A' }}</td>
                                    <td>{{ $recovery->waive_off_charges ?? '0' }}</td>
                                    <td>{{ ucfirst($recovery->total_amount) }}</td>
                                    <td>{{ ucfirst($recovery->payment_method) }}</td>
                                    <td>{{ ucfirst($recovery->status) }}</td>
                                    <td>
                                        {{ $recovery->remarks }}
                                        @if($recovery->is_early_settlement)
                                            <br>
                                            <b class="text-danger">
                                                {{ ($recovery->percentage) }}% of {{ ($recovery->remaining_amount) }}
                                                is {{ ($recovery->erc_amount) }}
                                            </b><br>
                                        @endif
                                    </td>
                                    <td>{{ showDate($recovery->recovery_date) }}</td>

                                </tr>

                            @endforeach
                        @endforeach

                        </tbody>
                    </table>

                    <h2>Disbursement Detail</h2>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date</th>
                            <th>Disbursed By</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($installments as $installment)
                            <tr>
                                <td colspan="6" class="text-center fw-bold">
                                    {{ $installment->loanApplication->application_id ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ $installment->loanApplication->transaction->transaction_reference }}</td>
                                <td>{{ $installment->loanApplication->transaction->amount }}</td>
                                <td>{{ ucfirst($installment->loanApplication->transaction->payment_method) }}</td>
                                <td>{{ ucfirst($installment->loanApplication->transaction->status) }}</td>
                                <td>{{ $installment->loanApplication->transaction->remarks }}</td>
                                <td>{{ showDate($installment->loanApplication->transaction->dateTime) }}</td>
                                <td>{{ $installment->loanApplication->transaction->user->name }}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
