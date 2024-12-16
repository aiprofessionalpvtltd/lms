<?php

namespace App\Http\Controllers\Admin;

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

    public function makePayment(string $accessToken, array $paymentData): JsonResponse
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

    public function store(Request $request)
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


            $paymentResponse = $this->makePayment($accessToken, $paymentData)->getData(true);

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

    public function storeManual(Request $request)
    {

        dd($request->all());
        $request->validate([
            'installment_detail_id_disbursement' => 'required',
            'disbursement_amount' => 'required',
            'payment_method' => 'required',
            'remarks' => 'required|',
        ]);

        DB::beginTransaction();

        try {
            $installment = Installment::find($request->installment_detail_id_disbursement);

            $loanApplication = LoanApplication::with('getLatestInstallment.details')
                ->findOrFail($installment->loan_application_id);

            $disburseAmount = $request->disbursement_amount ;


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
                'dateTime' => currentDateTimeInsert(),
            ]);




            DB::commit();

            return redirect()->route('show-installment')->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


}
