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

        <!-- Recovery details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recovery Payments</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped datatables-reponsive">
                    <thead>
                    <tr>
                        <th>Recovery ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installment->details->flatMap->recoveries as $recovery)
                        <tr>
                            <td>{{ $recovery->id }}</td>
                            <td>{{ $recovery->amount }}</td>
                            <td>{{ ucfirst($recovery->payment_method) }}</td>
                            <td>{{ ucfirst($recovery->status) }}</td>
                            <td>{{ $recovery->remarks }}</td>
                            <td>{{ $recovery->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>


    </div>
    <!-- /content area -->
@endsection

@push('script')
    <script src="{{ asset('backend/js/datatables.js') }}"></script>
@endpush
