@extends('admin.layouts.app')
@push('style')
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">{{ $title }}</span></h4>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content">
        <!-- Form validation -->
        <div class="card">
            <!-- Excel Upload Form -->
            <form action="{{ route('upload-nacta') }}" method="POST" enctype="multipart/form-data" class="flex-fill form-validate-jquery">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="file">Upload Excel File:</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        @error('file')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Submit button -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-end m-5">Upload</button>
                </div>
            </form>
            <!-- /Excel Upload Form -->
        </div>
        <!-- /form validation -->
    </div>
@endsection

@push('script')

@endpush
