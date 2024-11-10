@extends('admin.layouts.app')

@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{$title}}</span></h4>
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
                    @can('create-products')
                        <div class="col-md-12 mt-5">
                            <a href="{{route('add-product')}}"
                               class="btn btn-outline-primary float-end"><b><i
                                        class="fas fa-plus"></i></b> Add Product
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <table id="products-table" class="table table-striped datatables-responsive">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Processing Fee</th>
                        <th>Interest Rate</th>
                        <th>Province</th>
                        <th>District</th>
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
            $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("show-product") }}', // replace with your correct route for loading data
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'price', name: 'price'},
                    {data: 'processing_fee', name: 'processing_fee'},
                    {data: 'interest_rate', name: 'interest_rate'},
                    {data: 'province', name: 'province.name'}, // Adjust if necessary based on your relationship
                    {data: 'district', name: 'district.name'}, // Adjust if necessary based on your relationship
                    {data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-center'}
                ]
            });
        });

    </script>
@endpush
