@extends('admin.layouts.app')

@push('style')
    <link href="{{ asset('backend/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold"></span>{{ $title }}</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <!-- Form validation -->
        <div class="card">
            <!-- Product form -->
            <form action="{{ route('store-product') }}" method="POST" class="flex-fill form-validate-jquery">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Select Vendor </label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Vendor"
                                                    name="vendor_id" id="vendor_id"
                                                    data-type="vendor"
                                                    class="form-control select2 vendor"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($vendors as $key => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('vendor_id'))
                                                <span class="text-danger">{{ $errors->first('vendor_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Select Vendor Products </label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Vendor"
                                                    name="vendor_product_id" id="vendor_product_id"
                                                    data-type="vendor_product"
                                                    class="form-control select2 vendor_product"
                                                    data-fouc>
                                                <option></option>

                                            </select>
                                            @if ($errors->has('vendor_product_id'))
                                                <span class="text-danger">{{ $errors->first('vendor_product_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Name -->
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Product Name <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ old('name') }}" placeholder="Product Name">
                                            <div class="form-control-feedback"><i class="icon-box text-muted"></i></div>
                                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Product Price <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="text" name="price" class="form-control"
                                                   value="{{ old('price') }}" placeholder="Product Price">
                                            <div class="form-control-feedback"><i class="icon-box text-muted"></i></div>
                                            @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Detail -->
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Product Detail</label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <textarea name="detail" class="form-control"
                                                      placeholder="Product Detail">{{ old('detail') }}</textarea>
                                            <div class="form-control-feedback"><i
                                                    class="icon-file-text2 text-muted"></i></div>
                                            @error('detail') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Processing Fee -->
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Processing Fee <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number" name="processing_fee" class="form-control"
                                                   value="{{ old('processing_fee') }}" placeholder="Processing Fee"
                                                   min="0" step="0.01">
                                            <div class="form-control-feedback"><i class="icon-cash text-muted"></i>
                                            </div>
                                            @error('processing_fee') <span
                                                class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Interest Rate -->
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Interest Rate <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <input type="number" name="interest_rate" class="form-control"
                                                   value="{{ old('interest_rate') }}" placeholder="Interest Rate"
                                                   min="0" step="0.01">
                                            <div class="form-control-feedback"><i class="icon-percent text-muted"></i>
                                            </div>
                                            @error('interest_rate') <span
                                                class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Province -->
                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Select Province </label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Province"
                                                    name="province_id" id="province_id"
                                                    data-type="product"
                                                    class="form-control select2 province"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($provinces as $key => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('province_id'))
                                                <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-4">
                                        <label class="col-form-label">Select District  </label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select District"
                                                    name="district_id" id="product_district"
                                                    data-type="registration"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>

                                            </select>
                                            @if ($errors->has('role_id'))
                                                <span class="text-danger">{{ $errors->first('role_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-outline-primary float-end">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /product form -->
        </div>
        <!-- /form validation -->
    </div>
    <!-- /content area -->

@endsection

@push('script')
    <script src="{{ asset('backend/vendor/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@endpush
