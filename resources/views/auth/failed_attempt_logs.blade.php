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
                <table id="logs-table" class="table table-striped datatables-responsive">
                    <thead>
                    <tr>
                         <th>Phone No</th>
                        <th>Ip Address</th>
                        <th>Attempt Date Time</th>

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
            $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("failed-attempt-logs") }}',
                columns: [
                     {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'ip_address', name: 'ip_address'},
                    {data: 'attempted_at', name: 'attempted_at'},

                ]
            });

        });
    </script>
@endpush
