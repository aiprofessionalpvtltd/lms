@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid p-0">


        <div class="content">

            <!-- Basic datatable -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Upcoming Installments Due Within the Next 7 Days
                    </h2>
                </div>

                <div class="card-body">
                    <table class="table table-striped datatables-reponsive">
                        <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Application Name</th>
                            <th>CNIC</th>
                            <th>Mobile No</th>
                            <th>Monthly Installment</th>
                            <th>Next Payment Date</th>


                        </tr>
                        </thead>
                        <tbody>
                        @foreach($upcomingInstalments as $installment)
                             <tr>
                                <td>{{ $installment['loan_id'] ?? 'N/A' }}</td>
                                <td>{{ $installment['user_name'] ?? 'N/A' }}</td>
                                <td>{{ $installment['cnic'] ?? 'N/A' }}</td>
                                <td>{{ $installment['mobile_no'] ?? 'N/A' }}</td>
                                <td>{{ $installment['installment_amount'] ?? 'N/A' }}</td>
                                <td>{{ showDate($installment['next_due_date'] ?? 'N/A')  }}</td>



                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /basic datatable -->

        </div>

    </div>

@endsection

@push('script')
    <script src="{{ asset('backend/js/datatables.js') }}"></script>



@endpush
