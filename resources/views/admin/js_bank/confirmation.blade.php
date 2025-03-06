@extends('admin.layouts.app')
@push('style')
    <link href="{{asset('backend/vendor/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('backend/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}" rel="stylesheet">

@endpush
@section('content')
    <!--**********************************
            Content body start
        ***********************************-->

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold"></span>{{$title}}
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>


        </div>

    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">


        <div class="card">
            <div class="card-body">
                <p><strong>Receiver Account Title:</strong> {{ $responseData['RecieverAccountTitle'] }}</p>
                <p><strong>Receiver Mobile Number:</strong> {{ $responseData['ReceiverMobileNumber'] }}</p>
                <p><strong>Transaction Amount:</strong> {{ $responseData['TransactionAmount'] }}</p>
                <p><strong>Company Name:</strong> {{ $responseData['CompanyName'] }}</p>
                <p><strong>Company Mobile:</strong> {{ $responseData['CustomerMobile'] }}</p>
            </div>
        </div>

        <form action="{{ route('jszindagi.wallet-to-wallet.confirm') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$id}}">
            <button type="submit" class="btn btn-success mt-3">Confirm Transaction</button>
        </form>

    </div>
    <!-- /content area -->
    <!--**********************************
        Content body end
    ***********************************-->

@endsection

@push('script')


@endpush
