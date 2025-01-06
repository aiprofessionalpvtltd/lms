@extends('admin.layouts.app')

@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{ $title }}</span></h4>
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
                        <a target="_blank" href="https://nfs.nacta.gov.pk/"
                           class="btn btn-outline-danger  "><b><i
                                    class="fas fa-download"></i></b> Download Excel Sheet </a>

                        <a href="{{ route('create-nacta') }}"
                           class="btn btn-outline-primary float-end"><b><i
                                    class="fas fa-upload"></i></b> Upload NACTA List </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="nacta-table" class="table table-striped datatables-responsive">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Father's Name</th>
                        <th>CNIC</th>
                        <th>Province</th>
                        <th>District</th>
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
            $('#nacta-table').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                ajax: '{{ route("show-nacta") }}',
                columns: [
                    {data: 'name', name: 'name', searchable: true},
                    {data: 'father_name', name: 'father_name', searchable: true},
                    {data: 'cnic', name: 'cnic', searchable: true},
                    {data: 'province', name: 'province', searchable: true },
                    {data: 'district', name: 'district', searchable: true },
                 ]
            });
        });
    </script>
@endpush
