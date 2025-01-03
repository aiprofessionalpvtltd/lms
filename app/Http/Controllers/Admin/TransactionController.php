<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{


    public function index($id)
    {
        $title = 'Disbursement';
        $installment = Installment::with([
            'details',
            'user',
            'loanApplication',
            'loanApplication.calculatedProduct',
            'recoveries'
        ])->find($id);
        $loanApplication = LoanApplication::with('user.profile')->find($id);


//        dd($id);
        return view("admin.disbursement.create", compact('id', 'title', 'loanApplication'));
    }

    public function storeDisbursement(Request $request)
    {

        if ($request->service_api == 'jazz_cash_mw') {
            $this->jazzCashMWAPI($request);
        }
        if ($request->service_api == 'jazz_cash_ibft') {
            $this->jazzCashIBFTAPI($request);
        }

    }


    public function getToken(): JsonResponse
    {
        // Define the endpoint and headers
        $url = 'https://gateway-sandbox.jazzcash.com.pk/token';
        $headers = [
            'Authorization' => 'Basic ' . base64_encode('your-client-id:your-client-secret'), // Replace with actual client credentials
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        // Define the request payload
        $data = [
            'grant_type' => 'client_credentials',
        ];

        try {
            // Make the HTTP POST request
            $response = Http::withHeaders($headers)
                ->asForm()
                ->post($url, $data);

            // Check if the request was successful
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token retrieved successfully.',
                    'data' => $response->json(),
                ]);
            }

            // Handle unsuccessful responses
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve token.',
                'error' => $response->json(),
            ], $response->status());
        } catch (RequestException $exception) {
            // Handle exceptions during the HTTP request
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the token.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function makePaymentMW(string $accessToken, array $paymentData): JsonResponse
    {
        // Define the endpoint and headers
        $url = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/srv6/api/wso2/mw/payment';
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        try {
            // Make the HTTP POST request
            $response = Http::withHeaders($headers)
                ->post($url, $paymentData);

            // Check if the request was successful
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully.',
                    'data' => $response->json(),
                ]);
            }

            // Handle unsuccessful responses
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment.',
                'error' => $response->json(),
            ], $response->status());
        } catch (RequestException $exception) {
            // Handle exceptions during the HTTP request
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getStatusDescription(string $responseCode): string
    {
        $statuses = [
            'G2P-T-400' => 'Bad Request',
            'G2P-T-205' => 'There is an issue with your transactions, kindly contact JazzCash before reprocessing the transaction.',
            'G2P-T-500' => 'There is an issue with your transactions, kindly contact JazzCash before reprocessing the transaction.',
            'G2P-T--1' => 'System internal error.',
            'G2P-T-0' => 'The service request is processed successfully.',
            'G2P-T-1' => 'System busy. Please try again later.',
            'G2P-T-2' => 'Original Transaction is expired.',
            'G2P-T-10' => 'Request Message Structure is invalid.',
            'G2P-T-11' => 'Request Message is invalid.',
            'G2P-T-13' => 'The OriginatorConversationID is duplicated.',
            'G2P-T-17' => 'The security credential is invalid.',
            'G2P-T-2000' => 'Initiator Authentication Error.',
            'G2P-T-2001' => 'Receiver is invalid.',
            'G2P-T-2002' => 'Transaction information is invalid.',
            'G2P-T-2005' => 'Cannot match the reason type.',
            'G2P-T-2006' => 'Limit is breached.',
            'G2P-T-2008' => 'Amount is invalid.',
            'G2P-T-2009' => 'Insufficient Balance.',
            'G2P-T-2010' => 'The MSISDN don’t match with the CNIC.',
            'G2P-T-2015' => 'Not able to process in the third party.',
            'G2P-T-2016' => 'Transaction expired.',
            'G2P-T-2017' => 'Original Transaction is not complete.',
            'G2P-T-2018' => 'Original Transaction is not successful.',
            'G2P-T-2024' => 'Sender/Receiver MSISDN/CNIC Identical.',
            'G2P-T-30008' => 'Invalid Bank Account.',
            'G2P-T-97' => 'There was a problem with your request. Please recheck the parameters/format and try again.',
            'G2P-T-98' => 'Some Parameter is missing or invalid.',
            'G2P-T-99' => 'There is an issue with your transaction. Kindly contact Jazzcash before reprocessing the transaction.',
        ];

        return $statuses[$responseCode] ?? 'Unknown response code.';
    }

    public function jazzCashMWAPI($request)
    {
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
        ]);

        DB::beginTransaction();

        try {
            $request->merge(['payment_method' => 'Bank']);

            $loanApplication = LoanApplication::with('getLatestInstallment.details')
                ->findOrFail($request->loan_application_id);

            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;

            $tokenResponse = $this->getToken()->getData(true);

            if (!$tokenResponse['success']) {
                throw new \Exception($tokenResponse['message']);
            }

            $accessToken = $tokenResponse['data']['access_token'];

            $paymentData = [
                'amount' => $disburseAmount,
                'loan_application_id' => $loanApplication->id,
                'receiverCNIC' => inputMaskDash($loanApplication->user->profile->cnic_no),
                'receiverMSISDN' => inputMaskDash($loanApplication->user->profile->mobile_no),
                'referenceId' => 'moneyMW_' . uniqid(),
            ];


            $paymentResponse = $this->makePaymentMW($accessToken, $paymentData)->getData(true);

            if (!$paymentResponse['success']) {
                throw new \Exception($this->getStatusDescription($paymentResponse['error']['code'] ?? 'Unknown error'));
            }

            $transaction = Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(),
                'amount' => $paymentResponse['data']['amount'],
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'transaction_reference' => $paymentData['referenceId'],
                'remarks' => $paymentResponse['data']['responseDescription'],
                'responseCode' => $paymentResponse['data']['responseCode'],
                'transactionID' => $paymentResponse['data']['transactionID'],
                'referenceID' => $paymentResponse['data']['referenceID'],
                'dateTime' => $paymentResponse['data']['dateTime'],
            ]);

            $installments = $loanApplication->getLatestInstallment->details;

            if ($installments->isEmpty()) {
                throw new \Exception('No installments found for this loan application.');
            }

            $startDate = Carbon::now();

            foreach ($installments as $installment) {
                $dueDate = $startDate->copy()->addMonths(1);

                $installment->update([
                    'issue_date' => $startDate,
                    'due_date' => $dueDate,
                ]);

                $startDate = $dueDate->copy()->addDay();
            }

            DB::commit();

            return redirect()->route('show-installment')->with('success', 'Transaction and installments updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function jazzCashIBFTAPI($request)
    {
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
        ]);

        DB::beginTransaction();

        try {
            $request->merge(['payment_method' => 'Bank']);

            $loanApplication = LoanApplication::with('getLatestInstallment.details', 'user.bank_account')
                ->findOrFail($request->loan_application_id);

            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;

            $userBankDetail = $loanApplication->user->bank_account;
            $tokenResponse = $this->getToken()->getData(true);

            if (!$tokenResponse['success']) {
                throw new \Exception($tokenResponse['message']);
            }

            $accessToken = $tokenResponse['data']['access_token'];

            $paymentData = [
                'bankAccountNumber' => $userBankDetail->account_number,
                'bankCode' => $userBankDetail->swift_code,
                'amount' => $disburseAmount,
                'receiverMSISDN' => inputMaskDash($loanApplication->user->profile->mobile_no),
                'referenceId' => 'moneyIBFT_' . uniqid(),
            ];


            $paymentResponse = $this->makePaymentIBFT($accessToken, $paymentData)->getData(true);

            if (!$paymentResponse['success']) {
                throw new \Exception($this->getStatusDescription($paymentResponse['error']['code'] ?? 'Unknown error'));
            }

            $transaction = Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(),
                'amount' => $paymentResponse['data']['amount'],
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'transaction_reference' => $paymentData['referenceId'],
                'remarks' => $paymentResponse['data']['responseDescription'] .
                    ' bankAccountNumber: ' . $paymentResponse['data']['bankAccountNumber'] .
                    ' receiverMSISDN: ' . $paymentResponse['data']['receiverMSISDN'] .
                    ' bankAccountNumber: ' . $paymentResponse['data']['bankAccountNumber'] .
                    ' bankName: ' . $paymentResponse['data']['bankName'] .
                    ' balance: ' . $paymentResponse['data']['balance']
                ,
                'responseCode' => $paymentResponse['data']['responseCode'],
                'transactionID' => $paymentResponse['data']['transactionID'],
                'referenceID' => $paymentResponse['data']['referenceID'],
                'dateTime' => $paymentResponse['data']['dateTime'],
            ]);

            $installments = $loanApplication->getLatestInstallment->details;

            if ($installments->isEmpty()) {
                throw new \Exception('No installments found for this loan application.');
            }

            $startDate = Carbon::now();

            foreach ($installments as $installment) {
                $dueDate = $startDate->copy()->addMonths(1);

                $installment->update([
                    'issue_date' => $startDate,
                    'due_date' => $dueDate,
                ]);

                $startDate = $dueDate->copy()->addDay();
            }

            DB::commit();

            return redirect()->route('show-installment')->with('success', 'Transaction and installments updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function makePaymentIBFT(string $accessToken, array $paymentData): JsonResponse
    {
        // Define the endpoint and headers
        $url = 'https://gateway-sandbox.jazzcash.com.pk /jazzcash/third-party-integration/srv2/api/wso2/ibft/inquiry';
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        try {
            // Make the HTTP POST request
            $response = Http::withHeaders($headers)
                ->post($url, $paymentData);

            // Check if the request was successful
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully.',
                    'data' => $response->json(),
                ]);
            }

            // Handle unsuccessful responses
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment.',
                'error' => $response->json(),
            ], $response->status());
        } catch (RequestException $exception) {
            // Handle exceptions during the HTTP request
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    //////////////////////////////////////////////////////////////////////////////////////////
    public function storeManual(Request $request)
    {

        $request->validate([
            'installment_detail_id_disbursement' => 'required',
            'disbursement_amount' => 'required',
            'disbursement_date' => 'required',
            'payment_method' => 'required',
            'remarks' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $installment = Installment::find($request->installment_detail_id_disbursement);

            $loanApplication = LoanApplication::with('getLatestInstallment.details')
                ->findOrFail($installment->loan_application_id);

            $disburseAmount = $request->disbursement_amount;


            $paymentData = [
                'amount' => $disburseAmount,
                'loan_application_id' => $loanApplication->id,
                'receiverCNIC' => inputMaskDash($loanApplication->user->profile->cnic_no),
                'receiverMSISDN' => inputMaskDash($loanApplication->user->profile->mobile_no),
                'referenceId' => 'money_' . uniqid(),
            ];


            $transaction = Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(),
                'amount' => $disburseAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'transaction_reference' => $paymentData['referenceId'],
                'remarks' => $request->remarks,
                'responseCode' => 500,
                'transactionID' => uniqid(),
                'referenceID' => $paymentData['referenceId'],
                'dateTime' => dateInsert($request->disbursement_date),
            ]);

            // Set issue and due dates for installments
            $installmentDetails = $loanApplication->getLatestInstallment->details;
            $currentDate = Carbon::parse($request->disbursement_date);

            foreach ($installmentDetails as $index => $detail) {
                if ($index === 0) {
                    // First installment starts on disbursement date
                    $detail->issue_date = $currentDate->toDateString();
                } else {
                    // Subsequent installments
                    $detail->issue_date = $currentDate->addMonths(1)->toDateString();
                }

                // Due date is one month after issue date
                $detail->due_date = Carbon::parse($detail->issue_date)->addMonths(1)->toDateString();

                // Save the updated details
                $detail->save();
            }


            LogActivity::addToLog('Manual Disbursement of loan application ' . $installment->loanApplication->application_id . ' Created');

//            dd($transaction);
            DB::commit();

            return redirect()->back()->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function UpdateManual(Request $request)
    {
        $request->validate([
            'disbursement_edit_id' => 'required',
            'disbursement_edit_amount' => 'required',
            'disbursement_edit_date' => 'required',
            'disbursement_edit_payment_method' => 'required',
            'disbursement_edit_remarks' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($request->disbursement_edit_id);

            // Check if the date has been changed
            $existingDate = $transaction->dateTime;
            $newDate = dateInsert($request->disbursement_edit_date);

            $transaction->update([
                'amount' => $request->disbursement_edit_amount,
                'payment_method' => $request->disbursement_edit_payment_method,
                'remarks' => $request->disbursement_edit_remarks,
                'dateTime' => $newDate,
            ]);

            if ($existingDate !== $newDate) {
                // Fetch related loan application and installments
                $loanApplication = LoanApplication::with('getLatestInstallment.details')
                    ->findOrFail($transaction->loan_application_id);

                $installmentDetails = $loanApplication->getLatestInstallment->details;

                // Update issue_date and due_date based on new disbursement date
                $currentDate = Carbon::parse($newDate);

                foreach ($installmentDetails as $index => $detail) {
                    if ($index === 0) {
                        // First installment starts on the new date
                        $detail->issue_date = $currentDate->toDateString();
                    } else {
                        // Subsequent installments
                        $detail->issue_date = $currentDate->addMonths(1)->toDateString();
                    }

                    // Due date is one month after issue date
                    $detail->due_date = Carbon::parse($detail->issue_date)->addMonths(1)->toDateString();

                    // Save the updated details
                    $detail->save();
                }
            }

            LogActivity::addToLog('Manual Disbursement Updated and Installment Dates Adjusted');

            DB::commit();

            return redirect()->back()->with('success', 'Transaction and Installment dates updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


}
