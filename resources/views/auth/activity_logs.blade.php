@extends('admin.layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
@endpush
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
                         <th>User</th>
                         <th>Subject</th>
                         <th>Url</th>
                         <th>Method</th>
                        <th>Ip Address</th>
                        <th>Agent</th>
                        <th>Activity Date Time</th>

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
    <!-- Bootstrap 5 JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script>
        $(document).ready(function () {
            let table = $('#logs-table').DataTable({
                processing: true, // Show loading indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                scrollX: false, // Disable horizontal scrolling
                lengthChange: false,
                pageLength: 50, // Load 50 records first
                ajax: {
                    url: '{{ route("activity-logs") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'user', name: 'user'},
                    {data: 'subject', name: 'subject'},
                    {data: 'url', name: 'url'},
                    {data: 'method', name: 'method'},
                    {data: 'ip', name: 'ip'},
                    {data: 'agent', name: 'agent'},
                    {data: 'created_at', name: 'created_at'}
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary',
                        titleAttr: 'Print table',
                        exportOptions: { columns: ':visible', footer: true }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-danger',
                        titleAttr: 'Export to PDF',
                        orientation: 'landscape',
                        title: 'Activity Logs',
                        exportOptions: { columns: ':visible', footer: true }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-success',
                        titleAttr: 'Export to Excel',
                        title: 'Activity Logs',
                        exportOptions: { columns: ':visible', footer: true }
                    }
                ],
                drawCallback: function(settings) {
                    let api = this.api();
                    let info = api.page.info();

                    // If not all data is loaded, load next page automatically
                    if (info.recordsTotal > info.length && info.end < info.recordsTotal) {
                        setTimeout(function() {
                            api.page('next').draw('page');
                        }, 1000); // Adjust delay if needed
                    }
                }
            });
        });

    </script>
@endpush
