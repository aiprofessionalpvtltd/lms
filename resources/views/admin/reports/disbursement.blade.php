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
            <form action="{{ route('get-disbursement-report') }}" class="flex-fill form-validate-jquery">
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
                                                <option value="currentWeek">Current Week</option>
                                                <option value="currentMonth">Current Month</option>
                                                <option value="last3Months">Last 3 Months</option>
                                                <option value="last6Months">Last 6 Months</option>
                                                <option value="currentYear">Current Year</option> <!-- New option -->

                                            </select>
                                        </div>
                                    </div>
                                    <!-- Province -->
                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Date Range <span
                                                class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">

                                            <input type="text" name="date_range" class="form-control flatpickr-range"
                                                   placeholder="Select Date Range "/>

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
                                                <option></option>
                                                @foreach($genders as $key => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
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

                                    <div class="col-md-3 mt-4">
                                        <label class="col-form-label">Select District <span class="text-danger">*</span></label>
                                        <div class="form-group form-group-feedback form-group-feedback-right">
                                            <select data-placeholder="Select District"
                                                    name="district_id" id="product_district"
                                                    data-type="registration"
                                                    class="form-control select2"
                                                    data-fouc>
                                                <option></option>

                                            </select>
                                            @if ($errors->has('district_id'))
                                                <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-outline-primary float-end">Get Report
                                        </button>
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

    @if(isset($result))
        <!-- Basic datatable -->
            <div class="card">
                <div class="card-body">
                    <table id="datatables-buttons" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User Name</th>
                            <th>Province</th>
                            <th>District</th>
                            <th>Loan Amount</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($result as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->loanApplication->user->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->loanApplication->user->province->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->loanApplication->user->district->name ?? 'N/A' }}</td>
                                <td>{{ number_format($transaction->loanApplication->amount, 2) }}</td> <!-- Format amount -->
                                <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        $(document).ready(function() {
            // Datatables with Buttons
            var datatablesButtons = $("#datatables-buttons").DataTable({
                responsive: true,
                lengthChange: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copy',
                        className: 'btn btn-primary', // Bootstrap class for button styling
                        titleAttr: 'Copy to clipboard'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary', // Bootstrap class for button styling
                        titleAttr: 'Print table'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-danger', // Bootstrap class for button styling
                        titleAttr: 'Export to PDF',
                        title: 'Disbursement Report', // Change this to your report title
                        exportOptions: {
                            columns: ':visible' // Export only visible columns
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-success', // Bootstrap class for button styling
                        titleAttr: 'Export to Excel',
                        title: 'Disbursement Report', // Change this to your report title
                        exportOptions: {
                            columns: ':visible' // Export only visible columns
                        }
                    }
                ],
            });

            // Append buttons to the desired container
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
