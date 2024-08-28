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
                <h4><span class="font-weight-semibold"></span> Loan Application Detail
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>


        </div>


    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <div class="card mb-4">
            <div class="card-header">
                <h3>User Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $loanApplication->user->id }}</p>
                        <p><strong>Name:</strong> {{ $loanApplication->user->name }}</p>
                        <p><strong>Email:</strong> {{ $loanApplication->user->email }}</p>
                        <p><strong>Created At:</strong> {{ $loanApplication->user->created_at }}</p>
                        <p><strong>Updated At:</strong> {{ $loanApplication->user->updated_at }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Bank Name:</strong> {{ $loanApplication->user->bank_account->bank_name }}</p>
                        <p><strong>Account Title:</strong> {{ $loanApplication->user->bank_account->account_name }}</p>
                        <p><strong>Account Number:</strong> {{ $loanApplication->user->bank_account->account_number }}
                        </p>
                        <p><strong>IBAN:</strong> {{ $loanApplication->user->bank_account->iban }}</p>
                        <p><strong>Swift Code:</strong> {{ $loanApplication->user->bank_account->swift_code }}</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Profile Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>CNIC No:</strong> {{ $loanApplication->user->profile->cnic_no }}</p>
                        <p><strong>Issue Date:</strong> {{ $loanApplication->user->profile->issue_date }}</p>
                        <p><strong>Expire Date:</strong> {{ $loanApplication->user->profile->expire_date }}</p>
                        <p><strong>Date of Birth:</strong> {{ $loanApplication->user->profile->dob }}</p>
                        <p><strong>Mobile No:</strong> {{ $loanApplication->user->profile->mobile_no }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Profile Photo:</strong></p>
                        <img src="{{ asset('storage/' . $loanApplication->user->profile->photo) }}" alt="Profile Photo"
                             class="img-thumbnail" style="max-width: 150px;">
                        <p><strong>CNIC Photo:</strong></p>
                        <img src="{{ asset('storage/' . $loanApplication->user->profile->cnic) }}" alt="CNIC Photo"
                             class="img-thumbnail" style="max-width: 150px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Loan Application Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Loan Amount:</strong> {{ $loanApplication->loan_amount }}</p>
                        <p><strong>Loan Duration:</strong> {{ $loanApplication->loanDuration->name }}</p>
                        <p><strong>Product Service:</strong> {{ $loanApplication->productService->name }}</p>
                        <p><strong>Loan Purpose:</strong> {{ $loanApplication->loanPurpose->name }}</p>
                        <p><strong>Address:</strong> {{ $loanApplication->address }}</p>
                        <p><strong>Reference Contact 1:</strong> {{ $loanApplication->reference_contact_1 }}</p>
                        <p><strong>Reference Contact 2:</strong> {{ $loanApplication->reference_contact_2 }}</p>
                        <p><strong>Status:</strong> {{ $loanApplication->status }}</p>
                    </div>
                    <div class="col-md-6">
                        @if(count($loanApplication->attachments) > 0)
                            @foreach($loanApplication->attachments as $attachment)
                                <p><strong>{{$attachment->documentType->name}}</strong></p>
                                <img src="{{ asset('storage/' . $attachment->path) }}"
                                     alt="{{$attachment->documentType->name}}" class="img-thumbnail"
                                     style="max-width: 150px;">
                            @endforeach
                        @else
                            <p><strong>No Documents Uploaded</strong></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($loanApplication->guarantors)
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Loan Application Guarantor</h3>
                </div>
                <div class="card-body">
                    @foreach($loanApplication->guarantors as $guarantor)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>First Name:</strong> {{ $guarantor->first_name }}</p>
                                <p><strong>Last Name:</strong> {{ $guarantor->last_name }}</p>
                                <p><strong>CNIC No</strong> {{ $guarantor->cnic_no }}</p>
                                <p><strong>Address:</strong> {{ $guarantor->address }}</p>
                                <p><strong>Mobile No:</strong> {{ $guarantor->mobile_no }}</p>
                            </div>
                            <div class="col-md-6">

                                <p><strong>CNIC Attachment</strong></p>
                                <img src="{{ asset('storage/' . $guarantor->cnic_attachment) }}"
                                     alt="CNIC Attachment" class="img-thumbnail"
                                     style="max-width: 150px;">

                            </div>
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="card mb-4">

            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h3>Loan Application History</h3>
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

                            <h3>Update for proceeding</h3>

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
                                                {{$user->name . ' (' . $user->getRoleNames()[0] .')'}}
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
