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

                </div>
            </div>

            <div class="card-body">
                <table id="" class="table table-striped datatables-reponsive">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Asisgned To</th>
                    <th>Loan Amount</th>
                    <th>Duration</th>
                    <th>Service</th>
                    <th>Purpose</th>
                    <th>Address</th>
                    <th>Status</th>
                      <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($loanApplications as $loanApplication)
                    <tr>
                        <td>{{$loanApplication->name}}</td>
                        <td>{{$loanApplication->email}}</td>
                        <td>{{$loanApplication->getLatestHistory->toUser->name}}</td>
                        <td>{{$loanApplication->loan_amount}}</td>
                        <td>{{$loanApplication->loanDuration->name}}</td>
                        <td>{{$loanApplication->productService->name}}</td>
                        <td>{{$loanApplication->loanPurpose->name}}</td>
                        <td>{{$loanApplication->address}}</td>
                        <td>{{showApprovalStatus($loanApplication->status)}}</td>
                          <td>
                            <div class="d-flex">
                                @can('view-loan-management')
                                    <a title="View" href="{{ route('view-loan-application', $loanApplication->id) }}"
                                       class="text-primary mr-1"><i
                                            class="fas fa-eye"></i></a>
                                @endcan


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