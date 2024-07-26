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
                <h5 class="card-title"></h5>
                <div class="header-elements">
                    <a href="{{route('add-role')}}"
                       class="btn btn-outline-primary"><b><i
                                class="fas fa-plus"></i></b> Add {{$title}} </a>
                </div>
            </div>
            @include('admin.message')
            <div class="card-body">
                <table id="" class="table table-striped datatables-reponsive" style="width:100%">
                    <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{$role->name}}</td>
                            <td>{{$role->guard_name}}</td>
                            <td>
                                <div class="d-flex">

{{--                                    @can('edit-roles')--}}
                                        <a title="Edit" href="{{ route('edit-role', $role->id) }}"
                                           class="text-primary mr-1"><i
                                                class="fas fa-edit"></i></a>
{{--                                    @endcan--}}

{{--                                    @can('delete-roles')--}}
                                        <a href="javascript:void(0)" data-url="{{route('changeStatus-role')}}"
                                           data-status='0' data-label="inactive"
                                           data-id="{{$role->id}}"
                                           class=" text-danger mr-1 change-status-record "
                                           title="Suspend Record"><i class="fas fa-trash"></i></a>
{{--                                    @endcan--}}

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
    <script src=" {{asset('backend/custom/js/ajax_form.js')}}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Datatables Responsive
            $(".datatables-reponsive").DataTable({
                responsive: true
            });
        });
    </script>
@endpush
