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
                    <a href="{{route('add-account-transaction')}}"
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
                         <th> Date</th>
                        <th> Credit</th>
                        <th> Debit</th>
                        <th> Reference</th>
                        <th> Transaction Type</th>
                        <th> Description</th>
                         <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $row)
                        <tr>
                            <td>{{$row->account->code}}</td>
                            <td>{{$row->account->name}}</td>
                            <td>{{$row->account->accountName->name}}</td>
                             <td>{{showDate($row->date)}}</td>
                            <td class="text-danger">{{$row->credit > 0 ? $row->credit : ''}}</td>
                            <td class="text-success">{{$row->debit > 0 ? $row->debit : ''}}</td>
                             <td>{{$row->reference}}</td>
                            <td>{{$row->transaction_type}}</td>
                            <td>{{$row->description}}</td>
                             <td>
                                <div class="d-flex">

                                    <a title="Edit" href="{{ route('edit-account-transaction', $row->id) }}"
                                       class="text-primary mr-1"><i
                                            class="fas fa-edit"></i></a>

                                    <a href="javascript:void(0)" data-url="{{route('destroy-account-transaction')}}"
                                       data-status='0' data-label="delete"
                                       data-id="{{$row->id}}"
                                       class=" text-danger mr-1 change-status-record "
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
