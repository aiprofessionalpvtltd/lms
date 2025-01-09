@extends('admin.layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .star {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
        }
        .star.active {
            color: #ffc107;
        }
    </style>
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
                    <h4 class="text-center"><u>Customer Profile</u></h4>

                    <h6><u>CREDIT SANCTION FORM</u></h6>
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Client CNIC</th>
                                    <th>Cell #</th>
                                    <th>Score Level</th>
                                    <th>Risk Assessment</th>
                                     <th>Nacta Clear</th>

                                </tr>
                                <tr>
                                    <td>{{  $customer->profile->cnic_no  }}</td>
                                    <td>{{  $customer->profile->mobile_no  }}</td>
                                    <td>{{ $customer->tracking->score }}</td>
                                    <td>{{ $riskAssessment['risk_level'] }}</td>
                                    <td>{{ $customer->is_nacta_clear == 1 ?  'Clear' : 'Not Clear' }}</td>
                                </tr>

                            </table>

                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                            <div class="border-1">
                                <img src="{{ asset('storage/' . $customer->profile->photo) }}" width="100"
                                     height="100"
                                     alt="Profile Photo" class="img-thumbnail">
                            </div>
                        </div>
                    </div>


                    <!-- User Information Table -->
                    <h2>Client Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <th>Father/Husband Name</th>
                            <th>Gender</th>
                            <th>Nationality</th>
                            <th>Province</th>
                            <th>District</th>

                        <tr>
                            <td>{{ $customer->profile->first_name }} {{ $customer->profile->last_name }}</td>
                            <td>{{ $customer->profile->father_name }}</td>
                            <td>{{ $customer->profile->gender->name }}</td>
                            <td>{{ $customer->profile->nationality->name }}</td>
                            <td>{{ $customer->province->name }}</td>
                            <td>{{ $customer->district->name }}</td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <tr>
                            <th>City</th>
                            <th>Email</th>
                            <th>CNIC No</th>
                            <th>Issue Date</th>
                            <th>Expire Date</th>
                            <th>Date of Birth</th>

                        </tr>
                        <tr>
                            <td>{{ $customer->city->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->profile->cnic_no }}</td>
                            <td>{{ showDate($customer->profile->issue_date) }}</td>
                            <td>{{ showDate($customer->profile->expire_date) }}</td>
                            <td>{{ showDate($customer->profile->dob) }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th>Marital Status</th>
                            <th>Mobile No</th>
                            <th>Alternate Mobile No</th>
                            <th>Current Address Duration</th>
                            <th>Current Residence</th>
                        </tr>
                        <tr>
                            <td>{{ $customer->profile->maritalStatus->name }}</td>
                            <td>{{ $customer->profile->mobile_no }}</td>
                            <td>{{ $customer->profile->alternate_mobile_no }}</td>
                            <td>{{ $customer->profile->residenceDuration->name }}</td>
                            <td>{{ $customer->profile->residenceType->name }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th>Permanent Address</th>
                            <th>Current Address</th>
                        </tr>
                        <tr>
                            <td>{{ $customer->profile->permanent_address }}</td>
                            <td>{{ $customer->profile->current_address }}</td>
                        </tr>
                    </table>
                    <!-- Profile Documents Table -->
                    <h2>Profile Documents</h2>
                    <table class="table table-bordered">

                        <tr class="text-center">
                            <th>CNIC Front</th>
                            <th>CNIC Back</th>
                        </tr>
                        <tr class="text-center">
                            <td>
                                <a href="{{ asset('storage/' . $customer->profile->cnic_front) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $customer->profile->cnic_front) }}" width="100"
                                         height="100" alt="CNIC Front" class="img-thumbnail" style="max-width: 150px;">
                                </a>
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $customer->profile->cnic_back) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $customer->profile->cnic_back) }}" width="100"
                                         height="100" alt="CNIC Back" class="img-thumbnail" style="max-width: 150px;">
                                </a>
                            </td>
                        </tr>
                    </table>
                    <!-- Bank Information Table -->
                    <h2>Bank Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Bank Name</th>
                            <th>Account Title</th>
                            <th>Account Number</th>
                            <th>IBAN</th>
                            <th>Swift Code</th>

                        </tr>
                        <tr>
                            <td>{{ $customer->bank_account->bank_name }}</td>
                            <td>{{ $customer->bank_account->account_name }}</td>
                            <td>{{ $customer->bank_account->account_number }}</td>
                            <td>{{ $customer->bank_account->iban }}</td>
                            <td>{{ $customer->bank_account->swift_code }}</td>
                        </tr>
                    </table>

                    <!-- Family and Dependents Information Table -->
                    <h2>Family and Dependents Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>No of Dependents</th>
                            <th>Spouse Name</th>
                            <th>Spouse Employment Detail</th>

                        </tr>
                        <tr>
                            <td>{{ $customer->familyDependent->number_of_dependents }}</td>
                            <td>{{ $customer->familyDependent->spouse_name }}</td>
                            <td>{{ $customer->familyDependent->spouse_employment_details }}</td>
                        </tr>
                    </table>

                    <!-- Employment & Financial Information Table -->
                    <h2>Employment & Financial Information</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Employment Status</th>
                            <th>Income Source</th>
                            <th>Employer/Business Name</th>
                            <th>Position/Role</th>
                            <th>Years of Employment/Business</th>
                            <th>Monthly Income</th>
                            <th>Net Income</th>
                            <th>Existing Loan</th>

                        </tr>
                        <tr>
                            <td>{{ $customer->employment->employmentStatus->status }}</td>
                            <td>{{ $customer->employment->incomeSource->source }}</td>
                            <td>{{ $customer->employment->current_employer }}</td>
                            <td>{{ $customer->employment->job_title->name }}</td>
                            <td>{{ $customer->employment->employment_duration }}</td>
                            <td>{{ $customer->employment->gross_income }}</td>
                            <td>{{ $customer->employment->net_income }}</td>
                            <td>{{ $customer->employment->existingLoan->name }}</td>
                        </tr>
                    </table>

                @if(count($customer->references) > 0)

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
                    @endif

                    <h2>The reference risk categorization and scoring range is as under:</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th>Indicator</th>
                            <th>Scoring Range</th>
                            <th>Your Score Level</th>
                        </tr>
                        <tr>
                            <th>Low</th>
                            <th>0-22</th>
                            <th>
                                @if($customer->tracking->score >= 0 && $customer->tracking->score <= 22)
                                    <span class="text-success">{{ $customer->tracking->score }}</span>
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th>Medium</th>
                            <th>23-34</th>
                            <th>
                                @if($customer->tracking->score >= 23 && $customer->tracking->score <= 34)
                                    <span class="text-warning">{{ $customer->tracking->score }}</span>
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th>High</th>
                            <th>Greater Than or Equal To: 35</th>
                            <th>
                                @if($customer->tracking->score >= 35)
                                    <span class="text-danger">{{ $customer->tracking->score }}</span>
                                @endif
                            </th>
                        </tr>
                    </table>


                @if(count($loanApplications) > 0)
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
                                                    {{ ($recovery->percentage) }}%
                                                    of {{ ($recovery->remaining_amount) }}
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
                    @endif

                    <div id="commentForm">
                        <h4>Leave a Comment</h4>
                        <form id="commentBox">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div id="starRating" class="d-flex">
                                    <i class="star bi bi-star-fill" data-rating="1"></i>
                                    <i class="star bi bi-star-fill" data-rating="2"></i>
                                    <i class="star bi bi-star-fill" data-rating="3"></i>
                                    <i class="star bi bi-star-fill" data-rating="4"></i>
                                    <i class="star bi bi-star-fill" data-rating="5"></i>
                                    <i class="star bi bi-star-fill" data-rating="6"></i>
                                    <i class="star bi bi-star-fill" data-rating="7"></i>
                                    <i class="star bi bi-star-fill" data-rating="8"></i>
                                    <i class="star bi bi-star-fill" data-rating="9"></i>
                                    <i class="star bi bi-star-fill" data-rating="10"></i>
                                </div>
                            </div>
                            <button type="button" id="saveComment" class="btn btn-primary">Save</button>
                        </form>
                    </div>

                    <div id="commentDisplay" class="mt-5 d-none">
                        <h4>Your Comment</h4>
                        <p id="displayComment" class="fw-bold"></p>
                        <p id="displayRating" class="fw-bold"></p>
                    </div>

                    <div class="row signature-section mt-5">
                        <div class="col-6">
                            <p>Name Of
                                Customer: {{$customer->profile->first_name . ' ' . $customer->profile->last_name}}  </p>
                        </div>
                        <div class="col-6">
                            <p>Signature: _____________________________</p>
                        </div>
                    </div>

                    <div class="row signature-section mt-5">
                        <div class="col-6">
                            <p>CNIC No: {{$customer->profile->cnic_no}} </p>
                        </div>
                        <div class="col-6">
                            <p>Thumb Impression: ___________________</p>
                        </div>
                    </div>
                    <div class="row signature-section mt-5">
                        <div class="col-6">
                            <p>Mobile No: {{$customer->profile->mobile_no}} </p>
                        </div>
                        <div class="col-6">
                            <p>Dated : _________________________________</p>
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
            let selectedRating = 0;

            // Handle star rating selection
            $('.star').on('click', function () {
                selectedRating = $(this).data('rating');
                $('.star').removeClass('active');
                $(this).prevAll().addBack().addClass('active');
            });

            // Handle save button click
            $('#saveComment').on('click', function () {
                const comment = $('#comment').val();

                if (!comment || selectedRating === 0) {
                    alert('Please provide a comment and select a rating.');
                    return;
                }

                // Show the comment and rating in the display div
                $('#displayComment').text('Comment: ' + comment);
                $('#displayRating').text('Rating: ' + selectedRating + ' star(s)');
                $('#commentForm').hide();
                $('#commentDisplay').removeClass('d-none');
            });
        });

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
                     printDelay: 500, // Adjust delay if needed for large content
                    pageSize: 'A4', // Force the page size to A4
                    canvas: true, // Render canvas content if necessary

                });
            });
        });
    </script>
@endpush
