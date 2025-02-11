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


            <div class="card-body">
                <table  id="datatables-buttons" class="table table-bordered">
                    <thead>
                    <tr>
                        <th> Date</th>
                        <th> Code</th>
                        <th> Account Name</th>
                        <th> Credit</th>
                        <th> Debit</th>
                        <th> Reference</th>
                        <th> Transaction Type</th>
                        <th> Vendor Name</th>
                        <th> Description</th>
                     </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $row)
                        <tr>
                            <td>{{showDate($row->date)}}</td>
                            <td>{{$row->account->code}}</td>
                            <td>{{$row->account->accountName->name}}</td>
                            <td class="text-danger">{{$row->credit > 0 ? $row->credit : ''}}</td>
                            <td class="text-success">{{$row->debit > 0 ? $row->debit : ''}}</td>
                             <td>{{$row->reference}}</td>
                            <td>{{$row->transaction_type}}</td>
                            <td>{{ $row->vendorAccount ? $row->vendorAccount->name : 'N/A' }}</td>
                            <td>{{$row->description}}</td>

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
    <!-- Required Scripts -->

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

    <script src="{{asset('backend/js/datatables.js')}}"></script>


    <script>
        $(document).ready(function () {
            $('.select2').select2();

            var datatablesButtons = $("#datatables-buttons").DataTable({
                responsive: true,
                scrollX: false, // Enable horizontal scrolling
                lengthChange: false,
                pageLength: 100,
                buttons: [
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-danger',
                        titleAttr: 'Export to PDF',
                        orientation: 'landscape', // Set PDF orientation to landscape
                        title: 'General Ledger',
                        exportOptions: {
                            columns: ':visible',
                            footer: true // Include footer
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-success',
                        titleAttr: 'Export to Excel',
                        title: 'General Ledger',
                        exportOptions: {
                            columns: ':visible',
                            footer: true // Include footer
                        }
                    }
                ],
                dom: 'Bfrtip' // Position buttons above the table with search and length change controls
            });

            // Append buttons to a specific container if needed
            datatablesButtons.buttons().container().appendTo("#datatables-buttons_wrapper .col-md-6:eq(0)");
        });
    </script>

@endpush
