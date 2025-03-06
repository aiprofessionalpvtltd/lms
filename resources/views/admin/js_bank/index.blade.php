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
                <div class="col-md-12 mt-5">
                </div>

            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <span><b>Authorization Key : </b> {{env('js_zindagi_client_secret')}}</span>
                        <a class="btn btn-info" href="{{route('jszindagi.resetAuth')}}">Click to Reset JS Zindagi
                            AUthorization</a>
                    </div>

                    <div class="col-6">

                            @if(env('JS_ZINDAGI_ENCRYPTED_MPIN'))
                                <span><b> MPIN: </b> {{ env('JS_ZINDAGI_MPIN') }}</span> <br>
                                <span><b>Encrypted MPIN: </b> {{ env('JS_ZINDAGI_ENCRYPTED_MPIN') }}</span>
                            @else
                                <form method="POST" action="{{ route('jszindagi.generate.mpin') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-info">Generate MPIN</button>
                                </form>
                            @endif


                    </div>


                </div>
            </div>
        </div>
        <!-- /basic datatable -->

    </div>
    <!-- /content area -->
@endsection

@push('script')

@endpush
