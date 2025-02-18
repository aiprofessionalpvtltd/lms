@extends('admin.layouts.app')

@section('content')


    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">Loan Application</span>
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>

            </div>


        </div>

    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content">

        <!-- Basic datatable -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title"></h5>
                <div class="header-elements">
                    <div class="col-md-12 mt-5">

                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="" class="table table-striped datatables-reponsive">
                    <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Asisgned To</th>
                        <th>Loan Amount</th>
                        <th>Duration</th>
                         <th>Status</th>
                        <th>Completed</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loanApplications as $loanApplication)
                        <tr>
                            <td>{{$loanApplication->application_id}}</td>
                            <td>{{$loanApplication->name}}</td>
                            <td>{{$loanApplication->email}}</td>
                            <td>{{$loanApplication->getLatestHistory->toUser->name}}</td>
                            <td>{{$loanApplication->loan_amount}}</td>
                            <td>{{$loanApplication->loanDuration->name}}</td>
                             <td>{{ ucfirst($loanApplication->status) }}</td>
                            <td>{{showBoolean($loanApplication->is_completed)}}</td>
                            <td>
                                <div class="d-flex">
                                    <a title="Generate NOC"
                                       href="{{route('view-loan-noc', $loanApplication->id) }}"
                                       class=" btn btn-info me-3">Generate NOC</a>




                                </div>
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
    <script src="{{asset('backend/js/datatables.js')}}"></script>

@endpush
