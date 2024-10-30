@extends('admin.layouts.app')
@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{ $title }}</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->



    <!-- Content area -->
    <div class="content">

        <!-- Basic datatable -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Installment List</h5>
            </div>

            <div class="card-body">
                <table class="table table-striped datatables-reponsive">
                    <thead>
                    <tr>
                        <th>Application Name</th>
                        <th>Application Amount</th>
                        <th>Installment Duration</th>
                        <th>Total Payable Amount</th>
                        <th>Monthly Installment</th>
                        <th>Processing Fee</th>
                        <th>Total Markup</th>
                        <th>Approved By</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installments as $installment)
                        <tr>
                            <td>{{ $installment->loanApplication->name ?? 'N/A' }}</td>
                            <td>{{ $installment->loanApplication->loan_amount ?? 'N/A' }}</td>
                            <td>{{ $installment->loanApplication->loanDuration->name ?? 'N/A' }}</td>
                            <td>{{ $installment->total_amount }}</td>
                            <td>{{ $installment->monthly_installment }}</td>
                            <td>{{ $installment->processing_fee }}</td>
                            <td>{{ $installment->total_markup }}</td>
                            <td>{{ $installment->approvedBy->name ?? 'N/A' }}</td>

                            <td>
                                <a href="{{ route('view-installment', $installment->id) }}"
                                   class="btn btn-sm btn-info">View</a>

                                @if($installment->loanApplication->transaction)
                                    <a href="{{ route('view-installment', $installment->id) }}"
                                       class="btn btn-sm  btn-success ">Paid</a>
                                @else
                                    <form action="{{ route('transactions.store') }}" method="POST" class="d-inline">
                                    @csrf <!-- CSRF token for security -->
                                        <input type="hidden" name="loan_application_id" value="{{ $installment->loan_application_id }}">
                                          <button type="submit" class="btn btn-sm btn-success">Pay</button>
                                    </form>

                                @endif
                                <a href="{{ route('recovery.create', $installment->id) }}"
                                   class="btn btn-sm btn-info">Recovery</a>


                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /basic datatable -->

    </div>
    <!-- /content area -->
@endsection

@push('script')
    <script src="{{ asset('backend/js/datatables.js') }}"></script>
@endpush
