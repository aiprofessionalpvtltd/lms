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
                <h4><span class="font-weight-semibold">{{$title}}</span></h4>
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
            <form action="{{ route('get-principal-report') }}" class="flex-fill form-validate-jquery">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select Date Range <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select id="dateRangeSelector" name="dateRangeSelector"
                                                    class="form-control select2">
                                                <option value="">Select a Custom Date</option>
                                                <option
                                                    {{ request('dateRangeSelector') == 'currentWeek' ? 'selected' : '' }} value="currentWeek">
                                                    Current Week
                                                </option>
                                                <option
                                                    {{ request('dateRangeSelector') == 'currentMonth' ? 'selected' : '' }}  value="currentMonth">
                                                    Current Month
                                                </option>
                                                <option
                                                    {{ request('dateRangeSelector') == 'last3Months' ? 'selected' : '' }}  value="last3Months">
                                                    Last 3 Months
                                                </option>
                                                <option
                                                    {{ request('dateRangeSelector') == 'last6Months' ? 'selected' : '' }}  value="last6Months">
                                                    Last 6 Months
                                                </option>
                                                <option
                                                    {{ request('dateRangeSelector') == 'currentYear' ? 'selected' : '' }}  value="currentYear">
                                                    Current Year
                                                </option> <!-- New option -->

                                            </select>
                                        </div>
                                    </div>
                                    <!-- Province -->
                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Date Range <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="date_range" class="form-control flatpickr-range"
                                                   placeholder="Select Date Range " value="{{ request('date_range')}}"/>

                                        </div>
                                    </div>

                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select Gender <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Gender"
                                                    name="gender_id" id="gender_id"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option value="all">All</option>
                                                @foreach($genders as $key => $row)
                                                    <option
                                                        {{ request('gender_id') == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('gender_id'))
                                                <span class="text-danger">{{ $errors->first('gender_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Province -->
                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select Province <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Province"
                                                    name="province_id" id="province_id"
                                                    data-type="product"
                                                    class="form-control select2 province"
                                                    data-fouc>
                                                <option value="all">All</option>
                                                @foreach($provinces as $key => $row)
                                                    <option
                                                        {{ request('province_id') == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('province_id'))
                                                <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select District <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select District"
                                                    name="district_id" id="product_district"
                                                    data-type="registration"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                @if(request('province_id') )
                                                    @foreach($districts as $key => $row)
                                                        <option
                                                            {{ request('district_id') == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('district_id'))
                                                <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select Product <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select Product"
                                                    name="product_id" id="product_id"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>
                                                @foreach($products as $key => $row)
                                                    <option
                                                        {{ request('product_id') == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('product_id'))
                                                <span class="text-danger">{{ $errors->first('product_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-outline-primary float-end">Get Report
                                        </button>
                                        <a href="{{ route('show-principal-report') }}"
                                           class="btn btn-outline-dark me-3 float-end">Reset</a>
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

    @if(isset($principalData))
        <!-- Basic datatable -->
            <div class="card">
                <div class="card-body">
                    <table id="datatables-buttons" class="table table-bordered table-responsive">
                        <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Borrower Name</th>
                            <th>CNIC</th>
                            <th>Loan Amount</th>
                            <th>Principal Amount</th>
                            <th>Interest Amount</th>
                            <th>Interest + Principal</th>
                            <th>Installment Amount</th>
                            <th>Interest Received</th>
                            <th>Remaining Interest</th>
                            <th>Principal Received</th>
                            <th>Remaining Principal</th>


                        </thead>
                        <tbody>
                        @foreach($principalData as $row)
                            <tr>
                                <td>{{ $row['application_id'] ?? 'N/A' }}</td>
                                <td>{{ $row['borrower_name'] ?? 'N/A' }}</td>
                                <td>{{ $row['cnic'] ?? 'N/A' }}</td>
                                <td>{{ number_format($row['loan_amount'], 2) }}</td>
                                <td>{{ number_format($row['principal'], 2) }}</td>
                                <td>{{ ($row['interest_amount']) }}</td>
                                <td>{{ number_format($row['principal_plus_interest'], 2) }}</td>
                                <td>{{ number_format($row['installment_amount'], 2) }}</td>
                                <td>{{ number_format($row['interest_received'], 2) }}</td>
                                <td>{{ number_format($row['remaining_interest'], 2) }}</td>
                                <td>{{ number_format($row['remaining_principal'], 2) }}</td>
                                <td>{{ number_format($row['principal_received'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>


                </div>
            </div>
            <!-- /basic datatable -->

        @endif
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


    <script>
        $(document).ready(function () {
            $('.select2').select2();

            var datatablesButtons = $("#datatables-buttons").DataTable({
                responsive: true,
                scrollX: true, // Enable horizontal scrolling
                lengthChange: false,
                pageLength: 100,
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copy',
                        className: 'btn btn-primary',
                        titleAttr: 'Copy to clipboard'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary',
                        titleAttr: 'Print table',
                        exportOptions: {
                            columns: ':visible',
                            footer: true // Include footer
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-danger',
                        titleAttr: 'Export to PDF',
                        orientation: 'landscape', // Set PDF orientation to landscape
                        title: 'Principal Payment Report',
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
                        title: 'Principal Payment Report',
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

        document.addEventListener("DOMContentLoaded", function () {


// Initialize Flatpickr
            const flatpickrInstance = flatpickr(".flatpickr-range", {
                mode: "range",
                dateFormat: "Y-m-d",
            });

// Function to set date range
            function setDateRange(startDate, endDate) {
                flatpickrInstance.setDate([startDate, endDate]);
            }

// Event listener for dropdown
            $('#dateRangeSelector').on('change', function () {
                const selectedValue = $(this).val();
                let endDate = new Date();
                let startDate;

                if (selectedValue === "") {
                    // Reset the Flatpickr date range when no option is selected
                    flatpickrInstance.clear(); // Clear the date range
                    return; // Exit the function
                }

                switch (selectedValue) {
                    case "currentWeek":
                        startDate = new Date();
                        startDate.setDate(startDate.getDate() - startDate.getDay()); // Sunday
                        endDate.setDate(endDate.getDate() + (6 - endDate.getDay())); // Saturday
                        break;

                    case "currentMonth":
                        startDate = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
                        endDate = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0);
                        break;

                    case "last3Months":
                        startDate = new Date();
                        startDate.setMonth(endDate.getMonth() - 3);
                        break;

                    case "last6Months":
                        startDate = new Date();
                        startDate.setMonth(endDate.getMonth() - 6);
                        break;

                    case "currentYear": // Handle Current Year
                        startDate = new Date(new Date().getFullYear(), 0, 1); // January 1st
                        endDate = new Date(new Date().getFullYear(), 11, 31); // December 31st
                        break;

                    default:
                        return; // No valid option selected
                }

                setDateRange(startDate, endDate);
            });


        });
    </script>
@endpush
