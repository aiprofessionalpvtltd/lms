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
                    <div class="col-md-12 mt-5">
                        <a href="{{route('add-customer')}}"
                           class="btn btn-outline-primary float-end"><b><i
                                    class="fas fa-plus"></i></b> Add Customer </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="customers-table" class="table table-striped datatables-responsive">
                    <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone No</th>
                        <th>CNIC</th>
                        <th>Gender</th>
                        <th>Province</th>
                        <th>District</th>
                        <th>City</th>
                        <th>Score Level</th>
                        <th>Risk Assessment</th>
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
            $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("show-customer") }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'phone_no', name: 'profile.mobile_no'},
                    {data: 'cnic', name: 'profile.cnic_no'},
                    {data: 'gender', name: 'genders.name' ,searchable: false},
                    {data: 'province', name: 'provinces.name',searchable: false},
                    {data: 'district', name: 'districts.name',searchable: false},
                    {data: 'city', name: 'cities.name',searchable: false},
                    {data: 'score_level', name: 'tracking.score',searchable: false},
                    {data: 'risk_assessment', name: 'risk_assessment', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-center'}
                ]
            });

        });
    </script>
@endpush
