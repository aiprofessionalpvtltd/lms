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
                        <th>ID</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installment->details as $detail)
                        <tr>
                            <td>{{ $detail->id }}</td>
                            <td>{{ showDate($detail->due_date) }}</td>
                            <td>{{ $detail->amount_due }}</td>
                            <td>{{ $detail->amount_paid }}</td>
                            <td>{{ $detail->is_paid ? 'Paid' : 'Pending' }}</td>
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
                        <th>Transaction ID</th>
                        <th>Installment ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installment->recoveries as $recovery)
                        <tr>
                            <td>{{ $recovery->id }}</td>
                            <td>{{ $recovery->installment_detail_id }}</td>
                            <td>{{ $recovery->amount }}</td>
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
                <h5 class="card-title">Transaction Details</h5>
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
                    </tr>
                    </thead>
                    <tbody>
                    {{--                    @foreach($installment->loanApplication->transaction as $transaction)--}}
                    <tr>
                        <td>{{ $installment->loanApplication->transaction->id }}</td>
                        <td>{{ $installment->loanApplication->transaction->amount }}</td>
                        <td>{{ ucfirst($installment->loanApplication->transaction->payment_method) }}</td>
                        <td>{{ ucfirst($installment->loanApplication->transaction->status) }}</td>
                        <td>{{ $installment->loanApplication->transaction->remarks }}</td>
                        <td>{{ showDate($installment->loanApplication->transaction->created_at) }}</td>
                    </tr>
                    {{--                    @endforeach--}}
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Transaction details -->
    </div>
    <!-- /content area -->
@endsection
