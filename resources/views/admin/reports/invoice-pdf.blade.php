<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Noto Nastaliq Urdu', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 5px;
        }

        .invoice {
            border: 1px solid #ddd;
            padding: 5px;
        }

        .card-header {
            text-align: center;
            background-color: #17a2b8;
            color: white;
            padding: 5px;
        }

        .card-body {
            padding: 10px;
        }

        h3 {
            font-size: 12px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }

        .footer {
            font-size: 9px;
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="invoice" id="printInvoice">
        <div class="card shadow-lg">
            <div class="card-header text-center bg-info text-white py-4">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <img width="80" src="{{ $imageSrc }}" alt="Sarmaya Logo">
                    </div>
                    <div class="col-md-6 text-end">
                        <h2 class="mb-0 text-white">Sarmaya Microfinance (Private) Limited</h2>
                        <h4 class="text-white">{{ $title }}</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Invoice Details -->
                <h3>Invoice Details</h3>
                <table>
                    <tr>
                        <th>Invoice Number</th>
                        <td>{{ 'INV-' . now()->timestamp }}</td>
                        <th>Date</th>
                        <td>{{ now()->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td>{{ $invoiceData['borrower_name'] }}</td>
                        <th>CNIC</th>
                        <td>{{ $invoiceData['cnic'] }}</td>
                    </tr>
                    <tr>
                        <th>Mobile Number</th>
                        <td>{{ $invoiceData['mobile_no'] }}</td>
                        <th>Loan Account Number</th>
                        <td>{{ $invoiceData['loan_account_no'] }}</td>
                    </tr>
                </table>

                <!-- Loan Details -->
                <h3>Loan Details</h3>
                <table>
                    <tr>
                        <th>Total Loan Amount</th>
                        <td>{{ number_format($invoiceData['loan_amount'], 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Payable Amount</th>
                        <td>{{ number_format($invoiceData['total_payable'], 2) }}</td>
                    </tr>
                </table>

                <!-- Payment Details -->
                <h3>Payment Details</h3>
                <table>
                    <tr>
                        <th>#</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount Due (PKR)</th>
                        <th>Amount Paid (PKR)</th>
                        <th>Status</th>
                    </tr>
                    @php $isFirstInstallment = true; @endphp
                    @php $totalPaid = 0; $totalDue = 0; @endphp
                    @foreach($invoiceData['installments'] as $index => $detail)
                        @php
                            $totalPaid += $detail->amount_paid;
                            $totalDue += $detail->amount_due - $detail->amount_paid;
                        @endphp
                        <tr>
                            <td>{{ $detail->installment_number }}</td>
                            @if($isFirstInstallment)
                                <td>{{ showDate($detail->issue_date) }}</td>

                                @php $isFirstInstallment = false; @endphp
                            @else
                                <td></td>
                            @endif

                            <td>{{ showDate($detail->due_date) }}</td>
                            <td>{{ number_format($detail->amount_due, 2) }}</td>
                            <td>{{ number_format($detail->amount_paid, 2) }}</td>
                            <td>{{ $detail->status }}</td>
                        </tr>
                    @endforeach
                </table>

                <h3  class="border-bottom pb-2">Recovery Details</h3>

                <table>
                    <thead>
                    <tr>
                        <th>Installment</th>
                        <th>Installment Amount</th>
{{--                        <th>OverDue Days (PKR{{ env('LATE_FEE') }}/day)</th>--}}
{{--                        <th>Late Fee</th>--}}
{{--                        <th>Waive Off Charges</th>--}}
                        <th>Total Amount</th>
{{--                        <th>Payment Method</th>--}}
{{--                        <th>Status</th>--}}
{{--                        <th>Remarks</th>--}}
                        <th>Date</th>

                    </tr>
                    </thead>
                    <tbody>
                    @if(count($invoiceData['recoveries']) > 0)
                        @foreach($invoiceData['recoveries'] as $recovery)
                            <tr>
                                <td>{{ $recovery->installmentDetail->installment_number }}</td>
                                <td>{{ $recovery->amount }}</td>
{{--                                <td>{{ $recovery->overdue_days ?? 'N/A' }}</td>--}}
{{--                                <td>{{ $recovery->penalty_fee ?? 'N/A' }}</td>--}}
{{--                                <td>{{ $recovery->waive_off_charges ?? '0' }}</td>--}}
                                <td>{{ ucfirst($recovery->total_amount) }}</td>
{{--                                <td>{{ ucfirst($recovery->payment_method) }}</td>--}}
{{--                                <td>{{ ucfirst($recovery->status) }}</td>--}}
{{--                                <td>--}}
{{--                                    {{ $recovery->remarks }}--}}
{{--                                    @if($recovery->is_early_settlement)--}}
{{--                                        <br>--}}
{{--                                        <b class="text-danger">--}}
{{--                                            {{ ($recovery->percentage) }}%--}}
{{--                                            of {{ ($recovery->remaining_amount) }}--}}
{{--                                            is {{ ($recovery->erc_amount) }}--}}
{{--                                        </b><br>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td>{{ showDate($recovery->recovery_date) }}</td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center fw-bold">No Record Found</td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <!-- Summary -->
                <h3>Summary</h3>
                <table>
                    <tr>
                        <th>Total Paid</th>
                        <td>{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Outstanding Amount</th>
                        <td>{{ number_format($totalDue, 2) }}</td>
                    </tr>
                </table>

                <!-- Footer Notes -->
                <div class="footer">
                    <strong>Note:</strong>Please ensure timely payments to maintain a good credit record with Sarmaya Microfinance.
                    A penalty of PKR 200 per day will be applied for delayed payments as per the loan agreement.
                    <br>For any discrepancies or questions regarding this invoice, contact us at support@sarmayamf.com.
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
