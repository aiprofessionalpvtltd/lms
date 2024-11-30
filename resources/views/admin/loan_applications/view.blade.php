@extends('admin.layouts.app')
@push('style')
    <style>

        .timeline {
            list-style: none;
            padding: 0;
            position: relative;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 30px;
            width: 2px;
            background: #ddd;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-left: 60px;
        }

        .timeline-badge {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 50%;
            text-align: center;
            line-height: 60px;
            color: #007bff;
        }

        .timeline-content {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            position: relative;
        }

        .timeline-title {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .timeline-item.timeline-warning .timeline-badge {
            border-color: #ffc107;
            color: #ffc107;
        }

        .timeline-item.timeline-success .timeline-badge {
            border-color: #28a745;
            color: #28a745;
        }

        .timeline-item.timeline-danger .timeline-badge {
            border-color: #dc3545;
            color: #dc3545;
        }

        .timeline-item.timeline-warning .timeline-content {
            border-color: #ffc107;
        }

        .timeline-item.timeline-success .timeline-content {
            border-color: #28a745;
        }

        .timeline-item.timeline-danger .timeline-content {
            border-color: #dc3545;
        }

        .timeline-item:after {
            content: '';
            display: table;
            clear: both;
        }

    </style>
@endpush
@section('content')
    <!--**********************************
            Content body start
        ***********************************-->

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold"></span> Loan Application Detail ({{ $loanApplication->application_id ?? 'N/A'}})
                </h4>
            </div>


        </div>


    </div>
    <!-- /page header -->

    <a href="{{ route('view-customer', $loanApplication->user->id) }}" class="btn btn-primary">View Customer Detail</a>
    @if(count($previousLoans) > 0)
        <a href="{{ route('get-customer-loan-applications', ['id' => $loanApplication->user->id, 'loanID' => $loanApplication->id]) }}" class="btn btn-info">View All Applications</a>
    @endif

    <!-- Content area -->
    <div class="content">

        <div class="card mb-4">
{{--            <div class="card-header">--}}
{{--                <h2>Loan Application Information</h2>--}}
{{--            </div>--}}
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        {{--                        <p><strong>Loan Amount:</strong> {{ $loanApplication->loan_amount }}</p>--}}
                        {{--                        <p><strong>Loan Duration:</strong> {{ $loanApplication->loanDuration->name }}</p>--}}
{{--                        <p><strong>Product Service:</strong> {{ $loanApplication->productService->name }}</p>--}}
{{--                        <p><strong>Loan Purpose:</strong> {{ $loanApplication->loanPurpose->name }}</p>--}}
{{--                        <p><strong>Address:</strong> {{ $loanApplication->address }}</p>--}}
{{--                        <p><strong>Reference Contact 1:</strong> {{ $loanApplication->reference_contact_1 }}</p>--}}
{{--                        <p><strong>Reference Contact 2:</strong> {{ $loanApplication->reference_contact_2 }}</p>--}}
{{--                        <p><strong>Status:</strong> {{ $loanApplication->status }}</p>--}}

                        <br>
                        <h2>Applied for Product</h2>


                        <p><strong>Loan Amount:</strong> {{ $loanApplicationProduct->loan_amount }}</p>
                        <p><strong>Loan Type:</strong> {{ $loanApplicationProduct->request_for }}</p>
                        <p><strong>Product:</strong> {{ $loanApplicationProduct->product->name ?? 'N/A' }}</p>
                        <p><strong>Loan Duration :</strong> {{ $loanApplicationProduct->loanDuration->name }}</p>
                        <p><strong>Down Payment
                                Percentage:</strong> {{ $loanApplicationProduct->down_payment_percentage }}%</p>
                        <p><strong>Processing Fee
                                Percentage:</strong> {{ $loanApplicationProduct->processing_fee_percentage }}%</p>
                        <p><strong>Interest Rate
                                Percentage:</strong> {{ $loanApplicationProduct->interest_rate_percentage }}%</p>
                        <p><strong>Financed Amount:</strong> {{ $loanApplicationProduct->financed_amount }}</p>
                        <p><strong>Processing Fee Amount:</strong> {{ $loanApplicationProduct->processing_fee_amount }}
                        </p>
                        <p><strong>Down Payment Amount:</strong> {{ $loanApplicationProduct->down_payment_amount }}</p>
                        <p><strong>Total Upfront Payment:</strong> {{ $loanApplicationProduct->total_upfront_payment }}
                        </p>
                        <p><strong>Disbursement Amount:</strong> {{ $loanApplicationProduct->disbursement_amount }}</p>
                        <p><strong>Total Interest Amount:</strong> {{ $loanApplicationProduct->total_interest_amount }}
                        </p>
                        <p><strong>Total Repayable
                                Amount:</strong> {{ $loanApplicationProduct->total_repayable_amount }}</p>
                        <p><strong>Monthly Installment
                                Amount:</strong> {{ $loanApplicationProduct->monthly_installment_amount }}</p>


                    </div>
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

{{--                <h2>Loan Application Guarantors</h2>--}}

{{--                <table class="table" border="1" cellpadding="10" cellspacing="0">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>First Name</th>--}}
{{--                        <th>Last Name</th>--}}
{{--                        <th>CNIC No</th>--}}
{{--                        <th>Address</th>--}}
{{--                        <th>Mobile No</th>--}}
{{--                        <th>CNIC Attachment</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach($loanApplication->guarantors as $guarantor)--}}
{{--                        <tr>--}}
{{--                            <td>{{ $guarantor->first_name }}</td>--}}
{{--                            <td>{{ $guarantor->last_name }}</td>--}}
{{--                            <td>{{ $guarantor->cnic_no }}</td>--}}
{{--                            <td>{{ $guarantor->address }}</td>--}}
{{--                            <td>{{ $guarantor->mobile_no }}</td>--}}
{{--                            <td>--}}
{{--                                @if($guarantor->cnic_attachment)--}}
{{--                                    <a href="{{ asset('storage/' . $guarantor->cnic_attachment) }}" target="_blank">View--}}
{{--                                        Attachment</a>--}}
{{--                                @else--}}
{{--                                    N/A--}}
{{--                                @endif--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}


            </div>
        </div>


        <div class="card mb-4">

            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h2>Loan Application History</h2>
                        <ul class="timeline">
                            @foreach($loanApplication->histories as $history)
                                @php
                                    $statusClass = '';
                                    if ($history->status == 'pending') {
                                        $statusClass = 'timeline-warning';
                                    } elseif ($history->status == 'accepted') {
                                        $statusClass = 'timeline-success';
                                    } elseif ($history->status == 'rejected') {
                                        $statusClass = 'timeline-danger';
                                    }
                                @endphp
                                <li class="timeline-item {{ $statusClass }}">
                                    <div class="timeline-badge">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="timeline-details">
                                                <h6 class="mb-2 text-capitalize">
                                                    <strong>Assigned By:</strong> {{$history->fromUser->name}}
                                                    ({{$history->fromRole->name}})
                                                </h6>
                                                <h6 class="mb-2 text-capitalize">
                                                    <strong>Assigned To:</strong> {{$history->toUser->name}}
                                                    ({{$history->toRole->name}})
                                                </h6>
                                                <h6 class="text-capitalize">
                                                    <strong>Status:</strong> {{$history->status}}
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                {{ $history->created_at->format('F j, Y, g:i a') }}
                                            </small>
                                        </div>

                                        <p class="mb-1">
                                            <strong>Reason:</strong> {{ $history->remarks }}
                                        </p>

                                    </div>
                                </li>

                            @endforeach
                        </ul>
                    </div>
                    <div class="col-4">
                        <form action="{{ url('loan-applications/'.$loanApplication->id.'/status') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h2>Update for proceeding</h2>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label for="status" class="form-label"><strong>Status</strong></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option
                                            value="pending" {{ $loanApplication->status == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option
                                            value="accepted" {{ $loanApplication->status == 'accepted' ? 'selected' : '' }}>
                                            Accepted
                                        </option>
                                        <option
                                            value="rejected" {{ $loanApplication->status == 'rejected' ? 'selected' : '' }}>
                                            Rejected
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="status" class="form-label"><strong>Assigned To</strong></label>
                                    <select class="form-select" id="to_user_id" name="to_user_id" required>
                                        @foreach($toUsers as $user)
                                            <option value="{{$user->id}}">
                                                {{$user->name . ' (' . $user->province->name . ' ' . $user->district->name  .')'}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3" id="reason_div" style="display: block;">
                                <div class="col-md-12">
                                    <label for="remarks" class="form-label"><strong>Remarks</strong></label>
                                    <textarea class="form-control" id="remarks" name="remarks"
                                              rows="3"></textarea>
                                </div>
                            </div>

                            @if ($loanApplication->status != 'accepted')
                                <a href="{{route('approve-loan',$loanApplication->id)}}"
                                   class="btn btn-outline-success float-start">Approved</a>
                            @endif
                            <button type="submit" class="btn btn-primary float-end">Proceed</button>

                        </form>

                    </div>

                </div>


            </div>
        </div>


        <a href="{{ route('get-all-loan-applications') }}" class="btn btn-primary">Back to Application List</a>

    </div>
    <!-- /content area -->
    <!--**********************************
        Content body end
    ***********************************-->

@endsection

@push('script')


    {{--    <script>--}}
    {{--        document.addEventListener('DOMContentLoaded', function () {--}}
    {{--            const statusSelect = document.getElementById('status');--}}
    {{--            const reasonDiv = document.getElementById('reason_div');--}}

    {{--            function toggleReasonField() {--}}
    {{--                // if (statusSelect.value != 'pending') {--}}
    {{--                //     reasonDiv.style.display = 'block';--}}
    {{--                // } else {--}}
    {{--                    reasonDiv.style.display = 'none';--}}
    {{--                // }--}}
    {{--            }--}}

    {{--            statusSelect.addEventListener('change', toggleReasonField);--}}

    {{--            // Initialize the reason field visibility--}}
    {{--            toggleReasonField();--}}
    {{--        });--}}
    {{--    </script>--}}

@endpush
