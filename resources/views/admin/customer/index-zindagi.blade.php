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

                </div>
            </div>

            <div class="card-body">
                <table id="customers-table" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone No</th>
                        <th>CNIC</th>
                        <th>Gender</th>
                        <th>Province</th>
                        <th>District</th>
                        <th>City</th>
                         <th>Zindagi Verified</th>
                         <th>Zindagi Account Open</th>
                         <th>Account Opening Date</th>
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
                responsive:true,
                ajax: '{{ route("show-customer-zindagi") }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'phone_no', name: 'profile.mobile_no'},
                    {data: 'cnic', name: 'profile.cnic_no'},
                    {data: 'gender', name: 'genders.name' ,searchable: false},
                    {data: 'province', name: 'provinces.name',searchable: false},
                    {data: 'district', name: 'districts.name',searchable: false},
                    {data: 'city', name: 'cities.name',searchable: false},

                     {data: 'is_zindagi_verified', name: 'is_zindagi_verified', orderable: false, searchable: false},
                     {data: 'is_account_opened', name: 'is_zindagi_verified', orderable: false, searchable: false},
                     {data: 'account_opening_date', name: 'is_zindagi_verified', orderable: false, searchable: false},
                 ]
            });

        });
    </script>
@endpush
