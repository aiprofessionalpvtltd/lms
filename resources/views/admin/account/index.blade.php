@extends('admin.layouts.app')
@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{$title}}</span>
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
            <div class="card-header">
                <div class="col-md-12 mt-5">
                    <a href="{{route('add-account')}}"
                       class="btn btn-outline-primary float-end"><b><i
                                class="fas fa-plus"></i></b> Add {{$title}} </a>
                </div>

            </div>

            <div class="card-body">
                <table id="" class="table table-striped datatables-reponsive">
                    <thead>
                    <tr>
                        <th> Code</th>
                        <th> Name</th>
                        <th> Account Name</th>
                        <th> Account Type</th>
                        <th> Parent Account</th>
                        <th> Balance</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $row)
                        <tr>
                            <td>{{$row->code}}</td>
                            <td>{{$row->name}}</td>
                            <td>{{$row->accountName->name}}</td>
                            <td>{{$row->accountType->name}}</td>
                            <td>{{$row->parent->accountName->name ?? ''}}</td>
                            <td>{{$row->balance > 0 ?  $row->balance  : '0'}}</td>
                            <td>
                                <div class="d-flex">

                                    <a title="View History" target="_blank" href="{{ route('show-account-transaction-history', $row->id) }}"
                                       class="text-primary me-2"><i
                                            class="fas fa-history"></i></a>

                                    <a title="Edit" href="{{ route('edit-account', $row->id) }}"
                                       class="text-primary me-2"><i
                                            class="fas fa-edit"></i></a>

                                    <a href="javascript:void(0)" data-url="{{route('destroy-account')}}"
                                       data-status='0' data-label="delete"
                                       data-id="{{$row->id}}"
                                       class=" text-danger me-2 change-status-record "
                                       title="Suspend Record"><i class="fas fa-trash"></i></a>

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
