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
                    <a href="{{route('add-expense-category')}}"
                       class="btn btn-outline-primary float-end"><b><i
                                class="fas fa-plus"></i></b> Add {{$title}} </a>
                </div>

            </div>

            <div class="card-body">
                <table id="" class="table table-striped datatables-reponsive">
                    <thead>
                    <tr>
                        <th> Name</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $row)
                        <tr>
                            <td>{{$row->name}}</td>
                            <td>
                                <div class="d-flex">

                                    <a title="Edit" href="{{ route('edit-expense-category', $row->id) }}"
                                       class="text-primary mr-1"><i
                                            class="fas fa-edit"></i></a>

                                    <a href="javascript:void(0)" data-url="{{route('destroy-expense-category')}}"
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
