@extends('admin.layouts.app')
@section('content')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">Installment Details</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <a target="_blank" href="{{ route('view-customer', $installment->loanApplication->user->id) }}"
       class="btn btn-primary">View Customer Detail</a>
    <a target="_blank" href="{{ route('view-loan-application', $installment->loanApplication->id) }}"
       class="btn btn-info">View Loan Application Detail</a>

    <!-- Content area -->
    <div class="content">
        <!-- Installment summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Installment Summary</h5>
            </div>
            <div class="card-body">
                <p><strong>Customer Name:</strong> {{ $installment->user->name ?? 'N/A' }}</p>
                <p><strong>Application Name:</strong> {{ $installment->loanApplication->name ?? 'N/A' }}</p>
                <p><strong>Total Amount:</strong> {{ $installment->total_amount }}</p>
                <p><strong>Monthly Installment:</strong> {{ $installment->monthly_installment }}</p>
            </div>
        </div>

        <!-- Installment details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Installment Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        @can('edit-installments')
                            <th>Actions</th>
                        @endcan

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installment->details as $detail)
                        <tr data-id="{{ $detail->id }}">
                            <td>{{ $detail->installment_number }}</td>
                            {{--                            <td>--}}
                            {{--                                <span>{{ showDate($detail->issue_date) }} </span>--}}
                            {{--                            </td>--}}
                            <td>
                                <span class="issue-date-text">{{ showDate($detail->issue_date) }} </span>
                                <input type="date" class="issue-date-input d-none" value="{{ ($detail->issue_date) }}"/>
                            </td>
                            <td>
                                <span class="due-date-text">{{ showDate($detail->due_date) }} </span>
                                <input type="date" class="due-date-input d-none" value="{{ ($detail->due_date) }}"/>
                            </td>

                            <td>{{ $detail->amount_due }}</td>
                            <td>{{ $detail->amount_paid }}</td>
                            <td>{{ strtoupper($detail->status)  }}</td>
{{--                            @if($detail->is_paid == 0)--}}
                                @can('edit-installments')

                                    <td>
                                        @if($detail->is_paid == 0)
                                        <button class="btn  btn-sm btn-primary open-recovery-modal"
                                                data-id="{{ $detail->id }}"
                                                data-amount="{{ $detail->amount_due }}">
                                            Recover
                                        </button>
                                        <button class="btn btn-sm btn-primary open-early-modal"
                                                data-id="{{ $detail->id }}"
                                                data-amount="{{ number_format($detail->amount_due, 2) }}"
                                                data-remaining-loan="{{ number_format($detail->remaining_loan, 2) }}"
                                                data-penalty-percentage="{{ $detail->penalty_percentage }}"
                                                data-waive-off-changes="{{ $detail->waive_off_charges ?? '0' }}"
                                                data-total-payable="{{ number_format($detail->total_payable, 2) }}">
                                            Early Settlement
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-primary edit-due-date">Edit</button>
                                        <button class="btn btn-sm btn-danger cancel-update d-none">Cancel</button>
                                    </td>

                                @endcan
{{--                            @else--}}
{{--                                <td>--}}

{{--                                </td>--}}

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Installment details -->

    {{--        <table class="table table-bordered">--}}
    {{--            <thead>--}}
    {{--            <tr>--}}
    {{--                <th>#</th>--}}
    {{--                <th>Amount Due</th>--}}
    {{--                <th>Remaining Loan</th>--}}
    {{--                <th>ERC %</th>--}}
    {{--                <th>ERC Amount</th>--}}
    {{--                <th>Total Payable</th>--}}

    {{--            </tr>--}}
    {{--            </thead>--}}
    {{--            <tbody>--}}
    {{--            @foreach($unpaidInstallments as $key => $detail)--}}
    {{--                <tr>--}}
    {{--                    <td>{{ $key + 1 }}</td>--}}
    {{--                    <td>{{ number_format($detail->amount_due, 2) }}</td>--}}
    {{--                    <td>{{ number_format($detail->remaining_loan, 2) }}</td>--}}
    {{--                    <td>{{ $detail->penalty_percentage }}%</td>--}}
    {{--                    <td>{{ number_format($detail->penalty_amount, 2) }}</td>--}}
    {{--                    <td>{{ number_format($detail->total_payable, 2) }}</td>--}}

    {{--                </tr>--}}
    {{--            @endforeach--}}
    {{--            </tbody>--}}
    {{--        </table>--}}


    <!-- Bootstrap Modal -->
        <div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recoveryModalLabel">Installment Recovery</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="recoveryForm">
                        <div class="modal-body">
                            <input type="hidden" name="installment_detail_id" id="installment_detail_id">
                            <div class="mb-3">
                                <label for="installment_amount" class="form-label">Installment Amount</label>
                                <input type="number" readonly class="form-control" id="installment_amount" name="amount"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="overdue_days" class="form-label">Overdue Days</label>
                                <input type="number" class="form-control" id="overdue_days" name="overdue_days"
                                       value="0">
                            </div>
                            <div class="mb-3">
                                <label for="late_fee" class="form-label">Late Fee</label>
                                <input type="number" class="form-control" id="late_fee" name="late_fee" value="0">
                            </div>

                            <div class="mb-3">
                                <label for="waive_off_charges" class="form-label">Waive Off Charges</label>
                                <input type="number" class="form-control" id="waive_off_charges" name="waive_off_charges" value="0">
                            </div>


                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount"
                                       value="0">
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Recovery Date</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- First Modal (Early Settlement Details) -->
        <div class="modal fade" id="earlySettlementModal" tabindex="-1" aria-labelledby="earlySettlementModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="earlySettlementModalLabel">Early Settlement Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Installment ID</th>
                                <td id="modal-id"></td>
                            </tr>
                            <tr>
                                <th>Amount Due</th>
                                <td id="modal-amount"></td>
                            </tr>
                            <tr>
                                <th>Remaining Loan</th>
                                <td id="modal-remaining-loan"></td>
                            </tr>
                            <tr>
                                <th>ERC %</th>
                                <td id="modal-penalty-percentage"></td>
                            </tr>
                            <tr>
                                <th>ERC Amount</th>
                                <td id="modal-penalty-amount"></td>
                            </tr>
                            <tr>
                                <th>Total Payable</th>
                                <td id="modal-total-payable"></td>
                            </tr>
                        </table>
                        <form id="earlyForm">
                            <div class="modal-body">
                                <input type="hidden" name="installment_detail_id_early"
                                       id="installment_detail_id_early">
                                <input type="hidden" name="input_remaining_loan" id="input_remaining_loan">
                                <input type="hidden" name="input_penalty_percentage" id="input_penalty_percentage">
                                <input type="hidden" name="input_penalty_amount" id="input_penalty_amount">
                                <div class="mb-3">
                                    <label for="installment_amount_early" class="form-label">Installment Amount</label>
                                    <input type="text" readonly class="form-control" id="installment_amount_early"
                                           name="amount" required>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="bank">Bank</option>
                                        <option value="cash">Cash</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea class="form-control" id="remarks" name="remarks"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Confirm Settlement</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>


        <!-- Recovery details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recovery Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Installment</th>
                        <th>Installment Amount</th>
                        <th>OverDue Days (PKR{{ env('LATE_FEE') }}/day)</th>
                        <th>Late Fee</th>
                        <th>Waive Off Charges</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($installment->recoveries) > 0)
                        @foreach($installment->recoveries as $recovery)
                            <tr>
                                <td>{{ $recovery->installmentDetail->installment_number }}</td>
                                <td>{{ $recovery->amount }}</td>
                                <td>{{ $recovery->overdue_days ?? 'N/A' }}</td>
                                <td>{{ $recovery->penalty_fee ?? 'N/A' }}</td>
                                <td>{{ $recovery->waive_off_charges ?? '0' }}</td>
                                <td>{{ ucfirst($recovery->total_amount) }}</td>
                                <td>{{ ucfirst($recovery->payment_method) }}</td>
                                <td>{{ ucfirst($recovery->status) }}</td>
                                <td>
                                    {{ $recovery->remarks }}
                                    @if($recovery->is_early_settlement)
                                        <br>
                                        <b class="text-danger">
                                            {{ ($recovery->percentage) }}% of {{ ($recovery->remaining_amount) }}
                                            is {{ ($recovery->erc_amount) }}
                                        </b><br>
                                    @endif
                                </td>
                                <td>{{ showDate($recovery->created_at) }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-button"
                                            data-id="{{ $recovery->id }}"
                                            data-installment="{{ $recovery->installmentDetail->installment_number }}"
                                            data-amount="{{ $recovery->amount }}"
                                            data-overdue-days="{{ $recovery->overdue_days ?? 'N/A' }}"
                                            data-penalty-fee="{{ $recovery->penalty_fee ?? 'N/A' }}"
                                            data-waive-off-changes="{{ $recovery->waive_off_charges ?? '0' }}"
                                            data-total-amount="{{ $recovery->total_amount }}"
                                            data-payment-method="{{ $recovery->payment_method }}"
                                            data-status="{{ $recovery->status }}"
                                            data-remarks="{{ $recovery->remarks }}"
                                            data-is-early-settlement="{{ $recovery->is_early_settlement }}"
                                            data-percentage="{{ $recovery->percentage ?? '' }}"
                                            data-remaining-amount="{{ $recovery->remaining_amount ?? '' }}"
                                            data-erc-amount="{{ $recovery->erc_amount ?? '' }}"
                                            data-created-at="{{ dateInsert($recovery->created_at) }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center fw-bold">No Record Found</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Recovery Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editRecoveryForm">
                            <input type="hidden" id="recoveryId" name="recoveryId">

                            <div class="mb-3">
                                <label for="installment" class="form-label">Installment</label>
                                <input type="text" class="form-control" id="installment" name="installment">
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount">
                            </div>

                            <div class="mb-3">
                                <label for="overdueDays" class="form-label">Overdue Days</label>
                                <input type="text" class="form-control" id="overdueDays" name="overdueDays">
                            </div>

                            <div class="mb-3">
                                <label for="penaltyFee" class="form-label">Late Fee</label>
                                <input type="text" class="form-control" id="penaltyFee" name="penaltyFee">
                            </div>

                            <div class="mb-3">
                                <label for="waiveOffCharges" class="form-label">Waive Off Charges</label>
                                <input type="text" class="form-control" id="waiveOffCharges" name="waiveOffCharges">
                            </div>

                            <div class="mb-3">
                                <label for="totalAmount" class="form-label">Total Amount</label>
                                <input type="text" class="form-control" id="totalAmount" name="totalAmount">
                            </div>

                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Payment Method</label>
                                <input type="text" class="form-control" id="paymentMethod" name="paymentMethod">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status">
                            </div>


                            <div class="mb-3">
                                <label for="status" class="form-label">Percentage</label>
                                <input type="text" class="form-control" id="percentage" name="percentage">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Remaining Amount</label>
                                <input type="text" class="form-control" id="remainingAmount" name="remainingAmount">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">ERC Amount</label>
                                <input type="text" class="form-control" id="ercAmount" name="ercAmount">
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="recoveryDate" name="date">
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarksRecovery" name="remarks" rows="2"></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" form="editRecoveryForm">Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- /Recovery details -->

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="disbursementModal" tabindex="-1" aria-labelledby="disbursementModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="disbursementModalLabel">Disbursement Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="disbursementForm">
                        <div class="modal-body">
                            <input type="hidden" name="installment_detail_id_disbursement"
                                   id="installment_detail_id_disbursement">
                            <div class="mb-3">
                                <label for="disbursement_amount" class="form-label">Disburse Amount</label>
                                <input type="number" readonly class="form-control" id="disbursement_amount"
                                       name="disbursement_amount"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="disbursement_date" class="form-label">Disburse Date</label>
                                <input type="date"  class="form-control" id="disbursement_date"
                                       name="disbursement_date"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="disbursementEditModal" tabindex="-1" aria-labelledby="disbursementEditModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="disbursementEditModalLabel">Disbursement Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="disbursementEditForm">
                        <div class="modal-body">
                            <input type="hidden" name="disbursement_edit_id"
                                   id="disbursement_edit_id">
                            <div class="mb-3">
                                <label for="disbursement_edit_amount" class="form-label">Disburse Amount</label>
                                <input type="number" class="form-control" id="disbursement_edit_amount"
                                       name="disbursement_edit_amount"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="disbursement_edit_date" class="form-label">Disburse Date</label>
                                <input type="date"  class="form-control" id="disbursement_edit_date"
                                       name="disbursement_edit_date"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="disbursement_edit_payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="disbursement_edit_payment_method" name="disbursement_edit_payment_method" required>
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="disbursement_edit_remarks" name="disbursement_edit_remarks"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Transaction details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Disbursement Amount Details</h5>
                @if(!isset($installment->loanApplication->transaction))
                    <button class="btn btn-sm btn-success float-end disbursement-modal"
                            data-id="{{ $installment->id }}"
                            data-amount="{{ $installment->loanApplication->calculatedProduct->disbursement_amount }}">
                        Add
                        Disbursement
                    </button>
                @endif

            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Date</th>
                        <th>Disbursed By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(isset($installment->loanApplication->transaction))
                        <tr>
                            <td>{{ $installment->loanApplication->transaction->transaction_reference }}</td>
                            <td>{{ $installment->loanApplication->transaction->amount }}</td>
                            <td>{{ ucfirst($installment->loanApplication->transaction->payment_method) }}</td>
                            <td>{{ ucfirst($installment->loanApplication->transaction->status) }}</td>
                            <td>{{ $installment->loanApplication->transaction->remarks }}</td>
                            <td>{{ showDate($installment->loanApplication->transaction->dateTime) }}</td>
                            <td>{{ $installment->loanApplication->transaction->user->name }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-disburse-button"
                                        data-id="{{ $installment->loanApplication->transaction->id}}"
                                        data-amount="{{ $installment->loanApplication->transaction->amount  }}"
                                        data-reference="{{ $installment->loanApplication->transaction->transaction_reference  }}"
                                        data-payment-method="{{ $installment->loanApplication->transaction->payment_method }}"
                                        data-remarks="{{ $installment->loanApplication->transaction->remarks }}"
                                        data-date="{{ dateInsert($installment->loanApplication->transaction->dateTime) }}"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center fw-bold">No Disbursement data found</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Transaction details -->
    </div>
    <!-- /content area -->
@endsection
@push('script')
    <script>
        $(document).ready(function () {

            // Open the first modal
            $('.open-early-modal').click(function () {
                // Fetch data attributes from the clicked button
                const id = $(this).data('id');
                const amount = $(this).data('amount');
                const remainingLoan = $(this).data('remaining-loan');
                const penaltyPercentage = $(this).data('penalty-percentage');
                const penaltyAmount = $(this).data('penalty-amount');
                const totalPayable = $(this).data('total-payable');

                // Set modal fields for the first modal
                $('#modal-id').text(id);
                $('#modal-amount').text(amount);
                $('#modal-remaining-loan').text(remainingLoan);
                $('#modal-penalty-percentage').text(penaltyPercentage + '%');
                $('#modal-penalty-amount').text(penaltyAmount);
                $('#modal-total-payable').text(totalPayable);

                $('#installment_detail_id_early').val(id);  // Set installment detail ID
                $('#installment_amount_early').val(totalPayable); // Set amount due in the second modal
                $('#input_penalty_amount').val(penaltyAmount);
                $('#input_remaining_loan').val(remainingLoan);
                $('#input_penalty_percentage').val(penaltyPercentage);

                // Show the first modal
                $('#earlySettlementModal').modal('show');
            });

            $('#earlyForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                const url = '/recovery/installment/early'; // Your API endpoint

                // Collect form data into FormData object
                const formData = new FormData(this); // Automatically includes all form inputs
                formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token if not included in the form

                storeRecoveryData(url, formData)
                    .then(response => {
                        $('#earlyModal').modal('hide'); // Close the modal
                        notyf.open({
                            type: 'success',
                            message: 'Recovery saved successfully.',
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                        location.reload(); // Reload page if necessary
                    })
                    .catch(error => {
                        const errorMessage = error.responseJSON?.message || 'Failed to save data.';
                        notyf.open({
                            type: 'error',
                            message: errorMessage,
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                    });
            });

            // Handle edit button click
            $(document).on('click', '.edit-due-date', function () {
                const row = $(this).closest('tr');
                const button = $(this);

                if (button.hasClass('updating')) {
                    // Call updateDate when button is in 'updating' mode
                    const detailId = row.data('id');
                    const newDueDate = row.find('.due-date-input').val();
                    const newIssueDate = row.find('.issue-date-input').val();

                    // Perform AJAX requests sequentially
                    updateDate(`/installment/details/${detailId}/update-due-date`, {due_date: newDueDate})
                        .then(() => updateDate(`/installment/details/${detailId}/update-issue-date`, {issue_date: newIssueDate}))
                        .then(() => {
                            // Toggle back to 'Edit' button after both updates
                            button.text('Edit').removeClass('updating');

                            // Update UI
                            row.find('.due-date-text').removeClass('d-none').text(newDueDate);
                            row.find('.due-date-input').addClass('d-none');

                            row.find('.issue-date-text').removeClass('d-none').text(newIssueDate);
                            row.find('.issue-date-input').addClass('d-none');

                            // Reload the page after updates
                            setTimeout(function () {
                                location.reload();
                            }, 1000); // Adjust delay as needed
                        })
                        .catch(() => {
                            // Handle errors if any of the updates fail
                            notyf.open({
                                type: 'error',
                                message: 'Failed to update date.',
                                duration: 5000,
                                ripple: true,
                                dismissible: true,
                                position: {x: 'right', y: 'top'},
                            });
                        });
                } else {
                    // Enter edit mode
                    button.text('Update').addClass('updating');
                    row.find('.cancel-update').removeClass('d-none');

                    // Toggle visibility for Due Date fields
                    row.find('.due-date-text').addClass('d-none');
                    row.find('.due-date-input').removeClass('d-none').focus();

                    // Toggle visibility for Issue Date fields
                    row.find('.issue-date-text').addClass('d-none');
                    row.find('.issue-date-input').removeClass('d-none');
                }
            });

            // Handle cancel button click
            $(document).on('click', '.cancel-update', function () {
                const row = $(this).closest('tr');
                const button = row.find('.edit-due-date');

                // Restore original values
                const originalDueDate = row.find('.due-date-text').text().trim();
                const originalIssueDate = row.find('.issue-date-text').text().trim();

                row.find('.due-date-input').val(originalDueDate).addClass('d-none');
                row.find('.due-date-text').removeClass('d-none');

                row.find('.issue-date-input').val(originalIssueDate).addClass('d-none');
                row.find('.issue-date-text').removeClass('d-none');

                // Reset button text and hide cancel button
                button.text('Edit').removeClass('updating');
                $(this).addClass('d-none');
            });


            // Helper function for sending AJAX requests
            function updateDate(url, data) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            ...data,
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function (response) {
                            notyf.open({
                                type: 'success',
                                message: 'Date updated successfully.',
                                duration: 5000,
                                ripple: true,
                                dismissible: true,
                                position: {x: 'right', y: 'top'},
                            });
                            resolve(response);
                        },
                        error: function (xhr) {
                            notyf.open({
                                type: 'error',
                                message: 'Failed to update date.',
                                duration: 5000,
                                ripple: true,
                                dismissible: true,
                                position: {x: 'right', y: 'top'},
                            });
                            reject(xhr);
                        }
                    });
                });
            }


            // Open Recovery Modal
            $(document).on('click', '.open-recovery-modal', function () {
                const installmentDetailId = $(this).data('id');
                const installmentAmount = $(this).data('amount');

                // Set hidden field value
                $('#installment_detail_id').val(installmentDetailId);
                $('#installment_amount').val(installmentAmount);

                $('#recoveryModal').modal('show');

            });

            // Handle Recovery Form Submission
            $('#recoveryForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                const url = '/recovery/installment/recover'; // Your API endpoint

                // Collect form data into FormData object
                const formData = new FormData(this); // Automatically includes all form inputs
                formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token if not included in the form

                storeRecoveryData(url, formData)
                    .then(response => {
                        $('#recoveryModal').modal('hide'); // Close the modal
                        notyf.open({
                            type: 'success',
                            message: 'Recovery saved successfully.',
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                        location.reload(); // Reload page if necessary
                    })
                    .catch(error => {
                        const errorMessage = error.responseJSON?.message || 'Failed to save data.';
                        notyf.open({
                            type: 'error',
                            message: errorMessage,
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                    });
            });

            // Helper function for AJAX form submission
            function storeRecoveryData(url, formData) {

                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false, // Required for FormData
                        contentType: false, // Required for FormData
                        success: resolve,
                        error: reject,
                    });
                });
            }

            // Open Recovery Modal
            $(document).on('click', '.disbursement-modal', function () {
                const installmentDetailId = $(this).data('id');
                const disbursementAmount = $(this).data('amount');


                // Set hidden field value
                $('#installment_detail_id_disbursement').val(installmentDetailId);
                $('#disbursement_amount').val(disbursementAmount);

                $('#disbursementModal').modal('show');

            });

            $('#disbursementForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                const url = '{{ url("/transactions/storeManual") }}'; // Use Laravel's `url` helper for dynamic URLs
                const formData = new FormData(this); // Automatically includes all form inputs
                formData.append('_token', '{{ csrf_token() }}'); // Ensure CSRF token is included

                // console.log(url); return false;
                storeRecoveryData(url, formData)
                    .then(response => {
                        $('#disbursementModal').modal('hide'); // Close the modal
                        notyf.open({
                            type: 'success',
                            message: 'Disbursement saved successfully.',
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                        location.reload(); // Reload page if necessary
                    })
                    .catch(error => {
                        let errorMessage = 'Failed to save data.';
                        if (error.responseJSON?.message) {
                            errorMessage = error.responseJSON.message;
                        } else if (error.responseText) {
                            errorMessage = error.responseText;
                        }
                        notyf.open({
                            type: 'error',
                            message: errorMessage,
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                    });
            });

            $('.edit-button').on('click', function () {
                const recoveryId = $(this).data('id');
                const installment = $(this).data('installment');
                const amount = $(this).data('amount');
                const overdueDays = $(this).data('overdue-days');
                const penaltyFee = $(this).data('penalty-fee');
                const waiveOffCharges = $(this).data('waive-off-changes');
                const totalAmount = $(this).data('total-amount');
                const paymentMethod = $(this).data('payment-method');
                const status = $(this).data('status');
                const remarks = $(this).data('remarks');
                const isEarlySettlement = $(this).data('is-early-settlement');
                const percentage = $(this).data('percentage');
                const remainingAmount = $(this).data('remaining-amount');
                const ercAmount = $(this).data('erc-amount');
                const date = $(this).data('created-at');

                console.log(waiveOffCharges);

                // Populate modal form fields
                $('#recoveryId').val(recoveryId);
                $('#installment').val(installment);
                $('#amount').val(amount);
                $('#overdueDays').val(overdueDays);
                $('#penaltyFee').val(penaltyFee);
                $('#waiveOffCharges').val(waiveOffCharges);
                $('#totalAmount').val(totalAmount);
                $('#paymentMethod').val(paymentMethod);
                $('#status').val(status);
                $('#recoveryDate').val(date);
                $('#remarksRecovery').val(remarks);


                // Early Settlement Details
                if (isEarlySettlement) {
                    $('#earlySettlementDetails').show();
                    $('#percentage').val(percentage);
                    $('#remainingAmount').val(remainingAmount);
                    $('#ercAmount').val(ercAmount);
                } else {
                    $('#earlySettlementDetails').hide();
                }

                // Open modal
                $('#editModal').modal('show');
            });

            $('#editRecoveryForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                // Collect form data
                const formData = $(this).serialize(); // Serializes the form fields into a query string

                const recoveryId = $('#recoveryId').val(); // Get the recovery ID
                const updateUrl = `/recovery/recoveries/${recoveryId}`; // Define your API endpoint

                $.ajax({
                    url: updateUrl,
                    method: 'PUT', // Use PUT or POST based on your backend logic
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function (response) {
                        notyf.open({
                            type: 'success',
                            message: 'Recovery updated successfully.',
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });

                        // Reload the page after updates
                        setTimeout(function () {
                            location.reload();
                        }, 2000); // Adjust delay as needed
                    },
                    error: function (xhr) {
                        // Handle error response
                        notyf.open({
                            type: 'error',
                            message: 'An error occurred: ' + xhr.responseJSON.message,
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                    }
                });
            });


            $(document).on('click', '.edit-disburse-button', function () {
                const id = $(this).data('id');
                const disburseAmount = $(this).data('amount');
                const paymentMethod = $(this).data('payment-method');
                const remarks = $(this).data('remarks');
                const date = $(this).data('date');

                // Set hidden field value
                $('#disbursement_edit_id').val(id);
                $('#disbursement_edit_amount').val(disburseAmount);
                $('#disbursement_edit_payment_method').val(paymentMethod);
                $('#disbursement_edit_remarks').val(remarks);
                $('#disbursement_edit_date').val(date);

                $('#disbursementEditModal').modal('show');

            });

            $('#disbursementEditForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                const url = '{{ url("/transactions/updateManual") }}'; // Use Laravel's `url` helper for dynamic URLs
                const formData = new FormData(this); // Automatically includes all form inputs
                formData.append('_token', '{{ csrf_token() }}'); // Ensure CSRF token is included

                // console.log(url); return false;
                storeRecoveryData(url, formData)
                    .then(response => {
                        $('#disbursementEditModal').modal('hide'); // Close the modal
                        notyf.open({
                            type: 'success',
                            message: 'Disbursement Updated successfully.',
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                        location.reload(); // Reload page if necessary
                    })
                    .catch(error => {
                        let errorMessage = 'Failed to update data.';
                        if (error.responseJSON?.message) {
                            errorMessage = error.responseJSON.message;
                        } else if (error.responseText) {
                            errorMessage = error.responseText;
                        }
                        notyf.open({
                            type: 'error',
                            message: errorMessage,
                            duration: 5000,
                            ripple: true,
                            dismissible: true,
                            position: {x: 'right', y: 'top'},
                        });
                    });
            });



        });

    </script>
@endpush
