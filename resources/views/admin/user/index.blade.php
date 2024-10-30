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
            <div class="card-header header-elements-inline">
                <h5 class="card-title"></h5>
                <div class="header-elements">
                    @can('create-users')
                        <div class="col-md-12 mt-5">

                            <a href="{{route('add-user')}}"
                               class="btn btn-outline-primary float-end"><b><i
                                        class="fas fa-plus"></i></b> {{$title}}
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <table id="users-table" class="table table-striped datatables-responsive">
                    <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Province</th>
                        <th>District</th>
                        <th>City</th>
                        <th>Role</th>
                         <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
        <!-- /basic datatable -->

    </div>
    <!-- /content area -->
@endsection

@push('script')
    <script src="{{ asset('backend/js/datatables.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("show-user") }}', // replace 'your-route-name' with the correct route for loading data
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'province.name', name: 'province.name'},
                    {data: 'district.name', name: 'district.name'},
                    {data: 'city.name', name: 'city.name'},
                    {data: 'role', name: 'role', orderable: false, searchable: false},
                     {data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-center'}
                ]
            });
        });
    </script>
@endpush

