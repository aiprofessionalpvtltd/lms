@extends('admin.layouts.app')
@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">Installment Details</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <a target="_blank" href="{{ route('view-customer', $installment->loanApplication->user->id) }}"
       class="btn btn-primary">View Customer Detail</a>
    <a target="_blank" href="{{ route('view-loan-application', $installment->loanApplication->id) }}"
       class="btn btn-info">View Loan Application Detail</a>

    <!-- Content area -->
    <div class="content">
        <!-- Installment summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Installment Summary</h5>
            </div>
            <div class="card-body">
                <p><strong>Customer Name:</strong> {{ $installment->user->name ?? 'N/A' }}</p>
                <p><strong>Application Name:</strong> {{ $installment->loanApplication->name ?? 'N/A' }}</p>
                <p><strong>Total Amount:</strong> {{ $installment->total_amount }}</p>
                <p><strong>Monthly Installment:</strong> {{ $installment->monthly_installment }}</p>
            </div>
        </div>

        <!-- Installment details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Installment Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        @can('edit-installments')
                            <th>Actions</th>
                        @endcan

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installment->details as $detail)
                        <tr data-id="{{ $detail->id }}">
                            <td>{{ $detail->installment_number }}</td>
                            <td>
                                <span>{{ showDate($detail->issue_date) }} </span>
                            </td>
                            <td>
                                <span class="due-date-text">{{ showDate($detail->due_date) }} </span>
                                <input type="date" class="due-date-input d-none" value="{{ ($detail->due_date) }}"/>
                            </td>
                            <td>{{ $detail->amount_due }}</td>
                            <td>{{ $detail->amount_paid }}</td>
                            <td>{{ $detail->is_paid ? 'Paid' : 'Pending' }}</td>
                            @if($detail->is_paid == 0)
                                @can('edit-installments')

                                    <td>
                                        <button class="btn btn-sm btn-primary edit-due-date">Edit</button>
                                    </td>

                                @endcan
                            @else
                                <td>

                            </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Installment details -->


        <!-- Recovery details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recovery Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Installment</th>
                        <th>Installment Amount</th>
                        <th>OverDue Days (PKR{{env('LATE_FEE')}}/day)</th>
                        <th>Late Fee</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installment->recoveries as $recovery)
                        <tr>
                            <td>{{ $recovery->installmentDetail->installment_number }}</td>
                            <td>{{ $recovery->amount }}</td>
                            <td>{{ $recovery->overdue_days ?? 'N/A' }}</td>
                            <td>{{ $recovery->penalty_fee ?? 'N/A' }}</td>
                            <td>{{ ucfirst($recovery->total_amount) }}</td>
                            <td>{{ ucfirst($recovery->payment_method) }}</td>
                            <td>{{ ucfirst($recovery->status) }}</td>
                            <td>{{ $recovery->remarks }}</td>
                            <td>{{ showDate($recovery->created_at) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Recovery details -->


        <!-- Transaction details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Disbursement Amount Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Created At</th>
                        <th>Disbursed By </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $installment->loanApplication->transaction->id }}</td>
                        <td>{{ $installment->loanApplication->transaction->amount }}</td>
                        <td>{{ ucfirst($installment->loanApplication->transaction->payment_method) }}</td>
                        <td>{{ ucfirst($installment->loanApplication->transaction->status) }}</td>
                        <td>{{ $installment->loanApplication->transaction->remarks }}</td>
                        <td>{{ showDate($installment->loanApplication->transaction->created_at) }}</td>
                        <td>{{ $installment->loanApplication->transaction->user->name }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Transaction details -->
    </div>
    <!-- /content area -->
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            // Handle edit button click
            $('.edit-due-date').on('click', function () {
                const row = $(this).closest('tr');
                row.find('.due-date-text').addClass('d-none');
                row.find('.due-date-input').removeClass('d-none').focus();
            });

            // Handle input blur (click outside)
            $('.due-date-input').on('blur', function () {
                const row = $(this).closest('tr');
                const detailId = row.data('id');
                const newDueDate = $(this).val();

                // Hide the input and show the span
                row.find('.due-date-text').removeClass('d-none').text(newDueDate);
                $(this).addClass('d-none');

                // Send AJAX request to update the due date
                $.ajax({
                    url: `/installment/details/${detailId}/update-due-date`, // Update to match your route
                    method: 'POST',
                    data: {
                        due_date: newDueDate,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function (response) {
                        notyf.open({
                            type: 'success',
                            message: "Due date updated successfully.",
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'}
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 1000); // Adjust delay as needed
                    },
                    error: function (xhr) {
                        notyf.open({
                            type: 'error',
                            message: "Failed to update due date.",
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'}
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 1000); // Adjust delay as needed

                    }
                });
            });
        });

    </script>
@endpush
