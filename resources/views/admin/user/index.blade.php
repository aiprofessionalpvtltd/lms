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

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home </a>
                    <span class="breadcrumb-item active">{{$title}}</span>
                </div>

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
                        <a href="{{route('add-user')}}"
                           class="btn bg-primary-400 btn-labeled btn-labeled-left rounded-round"><b><i
                                    class="fas fa-plus"></i></b> Add {{$title}}
                        </a>
                    @endcan
                </div>
            </div>
            @include('admin.message')

            <table class="table datatable-basic">
                <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->roles[0]->name}}</td>
                        <td>{{showStatus($user->status)}}</td>
                        <td>`
                            <div class="d-flex">
                                @can('edit-users')
                                    <a title="Edit" href="{{ route('edit-user', $user->id) }}"
                                       class="text-primary mr-1"><i
                                            class="fas fa-edit"></i></a>
                                @endcan

                                @can('delete-users')
                                    <a href="javascript:void(0)" data-url="{{route('changeStatus-user')}}"
                                       data-status='0' data-label="inactive"
                                       data-id="{{$user->id}}"
                                       class=" text-danger mr-1 change-status-record {{($user->status == '1') ? '' : 'd-none'}}"
                                       title="Suspend Record"><i class="fas fa-trash"></i></a>

                                    <a href="javascript:void(0)" data-url="{{route('changeStatus-user')}}"
                                       data-status='1' data-label="active"
                                       data-id="{{$user->id}}"
                                       class="text-success mr-1 change-status-record {{($user->status == '0') ? '' : 'd-none'}}"
                                       title="Active Record"><i class="fas fa-redo"></i></a>

                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- /basic datatable -->

    </div>
    <!-- /content area -->
@endsection

@push('script')
    <script src=" {{asset('assets/global_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
    <script src=" {{asset('assets/global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
    <script src=" {{asset('assets/global_assets/js/demo_pages/datatables_basic.js')}}"></script>
    <script src=" {{asset('assets/custom/js/ajax_form.js')}}"></script>

@endpush
