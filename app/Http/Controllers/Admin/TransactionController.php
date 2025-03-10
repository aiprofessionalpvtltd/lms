<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Installment;
use App\Models\LoanApplication;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\password;

class TransactionController extends Controller
{

    private $key;
    private $iv;

    public function __construct()
    {
        $this->key = env('JAZZ_CASH_PRODUCTION_KEY', null); // Must be 16 bytes
        $this->iv = env('JAZZ_CASH_PRODUCTION_IV', null);   // Must be 16 bytes
    }

    // Encrypt function with IV parameter
    public function encrypt($plaintext, $iv)
    {
        $cipher = 'AES-128-CBC';

        // Validate IV length
        if (strlen($iv) !== openssl_cipher_iv_length($cipher)) {
            throw new \Exception("Invalid IV length. Must be 16 bytes.");
        }

        // Convert array to JSON if it's not a string
        if (is_array($plaintext)) {
            $plaintext = json_encode($plaintext);
        }

        // Encrypt the plaintext
        $encrypted = openssl_encrypt($plaintext, $cipher, $this->key, OPENSSL_RAW_DATA, $iv);


        // Encode the result in HEX
        return bin2hex($encrypted);
    }

    // Decrypt function with IV parameter
    public function decrypt($hexCipherText, $iv)
    {
        $cipher = 'AES-128-CBC';

        // Validate IV length
        if (strlen($iv) !== openssl_cipher_iv_length($cipher)) {
            throw new \Exception("Invalid IV length. Must be 16 bytes.");
        }

        // Decode the HEX string
        $encrypted = hex2bin($hexCipherText);
        if ($encrypted === false) {
            throw new \Exception("Invalid HEX input for decryption.");
        }

        // Decrypt the data
        $decrypted = openssl_decrypt($encrypted, $cipher, $this->key, OPENSSL_RAW_DATA, $iv);

        // Decode JSON string back to array if applicable
//        $jsonDecoded = json_encode($decrypted);
//        return $jsonDecoded !== null ? $jsonDecoded : $decrypted;
        return $decrypted;
    }


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
        $userBankDetail = $loanApplication->user->bank_account;

        $customerBank = Bank::where('name', $userBankDetail->bank_name)->first();

        return view("admin.disbursement.create", compact('id', 'title', 'loanApplication', 'customerBank'));
    }

    public function storeDisbursement(Request $request)
    {
        try {
            // Call the respective API based on the service API type
            if ($request->service_api == 'jazz_cash_mw') {
                return $this->jazzCashMWAPI($request);
            }

            if ($request->service_api == 'js_zindagi_w2w') {
                return app(JSBankController::class)->walletToWalletInquiry($request);
            }

            if ($request->service_api == 'js_bank_ibft' || $request->service_api == 'js_bank_ift' || $request->service_api == 'js_bank_coc') {
                return redirect()->route('jsbank.ibftAPI')->withInput();
            }

            // Handle unsupported service APIs
            return redirect()->back()->withErrors(['error' => 'Invalid service API provided.']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


//    public function getTokenWithCurl()
//    {
//        // Define the endpoint and credentials
//        $url = 'https://gateway-sandbox.jazzcash.com.pk/token';
//        $stagingToken = 'MjlwT1BmSVBTRXRkZGY2THRVQjRtX2F5YjdvYTpGSnF1eTlIRjNySkVlYUNDZWs1RXZFa2xFRjBh';
//
//        // Initialize cURL
//        $curl = curl_init();
//
//        // Set cURL options
//        curl_setopt_array($curl, [
//            CURLOPT_URL => $url,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => http_build_query(['grant_type' => 'client_credentials']),
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_HTTPHEADER => [
//                'Authorization: Basic ' . $stagingToken,
//                'Content-Type: application/x-www-form-urlencoded',
//            ],
//        ]);
//
//        // Execute the cURL request
//        $response = curl_exec($curl);
//
//        // Check for cURL errors
//        if (curl_errno($curl)) {
//            $error = curl_error($curl);
//            curl_close($curl);
//
//            return response()->json([
//                'success' => false,
//                'message' => 'An error occurred while retrieving the token.',
//                'error' => $error,
//            ], 500);
//        }
//
//        // Get HTTP status code
//        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//
//        // Close the cURL session
//        curl_close($curl);
//
//        // Decode the JSON response
//        $decodedResponse = json_decode($response, true);
//
//        // Handle the response
//        if ($httpStatus >= 200 && $httpStatus < 300) {
//            return response()->json([
//                'success' => true,
//                'message' => 'Token retrieved successfully.',
//                'data' => $decodedResponse,
//            ]);
//        }
//
//        // Handle unsuccessful responses
//        return response()->json([
//            'success' => false,
//            'message' => $decodedResponse['error_description'] ?? 'Failed to retrieve token.',
//            'error' => $decodedResponse,
//        ], $httpStatus);
//    }

    public function getToken()
    {
        // Define the endpoint and headers
//        $url = 'https://gateway-sandbox.jazzcash.com.pk/token';
        $url = 'https://gateway.jazzcash.com.pk/token';
        $token = env('JAZZ_CASH_PRODUCTION_TOKEN');

        $headers = [
            'Authorization' => 'Basic ' . $token, // Replace with actual client credentials
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

            dd($headers, $response->status(), $response->json());
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
            dd('Handle exceptions', $exception->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the token.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    public function makePaymentMW(string $accessToken, array $paymentData)
    {
        // Define the endpoint and headers
        $url = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/srv6/api/wso2/mw/payment';
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        // Encrypt the payment data
        $encryptedPaymentData = $this->encrypt($paymentData, $this->iv);

        // Prepare the request payload
        $payload = [
            'data' => $encryptedPaymentData,
        ];

        try {
            // Make the HTTP POST request
            $response = Http::withHeaders($headers)->post($url, $payload);

            // Decode the JSON response
            $responseData = $response->json();

            // Check if the response contains encrypted data
            if (!isset($responseData['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from the payment gateway.',
                    'data' => $responseData,
                ], 400);
            }

            // Decrypt the response data
            $decryptedData = $this->decrypt($responseData['data'], $this->iv);
            $decryptedData = json_decode($decryptedData, true); // Decode as an associative array

            // Validate the decrypted data structure
            if (!is_array($decryptedData) || !isset($decryptedData['responseCode'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid decrypted data format.',
                    'data' => $decryptedData,
                ], 400);
            }

            // Handle the response based on the response code
            if ($decryptedData['responseCode'] === 'G2P-T-0') {
                return response()->json([
                    'success' => true,
                    'message' => isset($decryptedData['responseDescription']) ? $decryptedData['responseDescription'] : 'Payment processed successfully.',
                    'data' => $decryptedData,
                ]);
            }

            // Handle failure response
            return response()->json([
                'success' => false,
                'message' => isset($decryptedData['responseDescription']) ? $decryptedData['responseDescription'] : 'Payment failed.',
                'data' => $decryptedData,
            ]);
        } catch (RequestException $exception) {
            // Handle exceptions during the HTTP request
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    public function getStatusDescription(string $responseCode)
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

        return isset($statuses[$responseCode]) ? $statuses[$responseCode] : 'Unknown response code.';
    }

    public function jazzCashMWAPI($request)
    {
        // Validate request input
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
        ]);

        DB::beginTransaction();

        try {
            // Set default payment method
            $request->merge(['payment_method' => 'Bank']);

            // Fetch the loan application with related installments and details
            $loanApplication = LoanApplication::with('getLatestInstallment.details')
                ->findOrFail($request->loan_application_id);

            // Calculate the disbursement amount
            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;

            // Fetch the access token
            $tokenResponse = $this->getToken()->getData(true);

            dd($tokenResponse);
            if (!$tokenResponse['success']) {
                throw new \Exception($tokenResponse['message']);
            }

            $accessToken = $tokenResponse['data']['access_token'];

            // Prepare payment data
//            $paymentData = [
//                'receiverCNIC' => '9203000055897',
//                'receiverMSISDN' => '03000055897',
//                'amount' => '10.00',
//                'referenceId' => 'moneyMW_' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 10),
//            ];

            $paymentData = [
                'receiverCNIC' => '3320214988957',
                'receiverMSISDN' => '03336754846',
                'amount' => '10.00',
                'referenceId' => 'moneyMW_' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 10),
            ];

//            $paymentData = [
//                'amount' => $disburseAmount,
//                'loan_application_id' => $loanApplication->id,
//                'receiverCNIC' => inputMaskDash($loanApplication->user->profile->cnic_no),
//                'receiverMSISDN' => env('JAZZ_CASH_MSISDN'),
//                'referenceId' => 'moneyMW_' . uniqid(),
//            ];

            // Send payment request to the JazzCash API
            $paymentResponse = $this->makePaymentMW($accessToken, $paymentData)->getData(true);

            dd($paymentData, $paymentResponse);
            // Handle unsuccessful payment
            if (!$paymentResponse['success']) {
                throw new \Exception('Payment failed: ' . $paymentResponse['message']);
            }

            // Create a new transaction
            $transaction = Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(),
                'amount' => $paymentResponse['data']['amount'],
                'payment_method' => 'Jazz Cash MW',
                'status' => 'completed',
                'transaction_reference' => $paymentResponse['data']['referenceID'],
                'remarks' => $paymentResponse['data']['responseDescription'],
                'responseCode' => $paymentResponse['data']['responseCode'],
                'transactionID' => $paymentResponse['data']['transactionID'],
                'referenceID' => $paymentResponse['data']['referenceID'],
                'dateTime' => $paymentResponse['data']['dateTime'],
            ]);

            // Validate if installments exist
            $installments = $loanApplication->getLatestInstallment->details;

            if ($installments->isEmpty()) {
                throw new \Exception('No installments found for this loan application.');
            }

            // Update installment dates
            $startDate = Carbon::now();

            foreach ($installments as $installment) {
                $dueDate = $startDate->copy()->addMonths(1);

                $installment->update([
                    'issue_date' => $startDate,
                    'due_date' => $dueDate,
                ]);

                $startDate = $dueDate->copy()->addDay(); // Prepare for the next installment
            }

            // Commit transaction
            DB::commit();

            return redirect()->route('show-installment')->with('success', 'Transaction completed successfully.');

        } catch (\Exception $e) {
            // Rollback in case of any exception
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

//    public function jazzCashIBFTAPI($request)
//    {
//        $request->validate([
//            'loan_application_id' => 'required|exists:loan_applications,id',
//        ]);
//
//        DB::beginTransaction();
//
//        try {
//            $request->merge(['payment_method' => 'Bank']);
//
//            $loanApplication = LoanApplication::with('getLatestInstallment.details', 'user.bank_account')
//                ->findOrFail($request->loan_application_id);
//
//            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;
//
//            $userBankDetail = $loanApplication->user->bank_account;
//            $tokenResponse = $this->getToken()->getData(true);
//
//            if (!$tokenResponse['success']) {
//                throw new \Exception($tokenResponse['message']);
//            }
//
//            $accessToken = $tokenResponse['data']['access_token'];
//
//            $paymentData = [
//                'bankAccountNumber' => $userBankDetail->account_number,
//                'bankCode' => $userBankDetail->swift_code,
//                'amount' => $disburseAmount,
//                'receiverMSISDN' => inputMaskDash($loanApplication->user->profile->mobile_no),
//                'referenceId' => 'moneyIBFT_' . uniqid('', true),
//            ];
//
//
//            $paymentResponse = $this->makePaymentIBFT($accessToken, $paymentData)->getData(true);
//
//            if (!$paymentResponse['success']) {
//                throw new \Exception($this->getStatusDescription(isset($paymentResponse['error']['code']) ? $paymentResponse['error']['code'] : 'Unknown error'));
//            }
//
//            $transaction = Transaction::create([
//                'loan_application_id' => $loanApplication->id,
//                'user_id' => Auth::id(),
//                'amount' => $paymentResponse['data']['amount'],
//                'payment_method' => $request->payment_method,
//                'status' => 'completed',
//                'transaction_reference' => $paymentData['referenceId'],
//                'remarks' => $paymentResponse['data']['responseDescription'] .
//                    ' bankAccountNumber: ' . $paymentResponse['data']['bankAccountNumber'] .
//                    ' receiverMSISDN: ' . $paymentResponse['data']['receiverMSISDN'] .
//                    ' bankAccountNumber: ' . $paymentResponse['data']['bankAccountNumber'] .
//                    ' bankName: ' . $paymentResponse['data']['bankName'] .
//                    ' balance: ' . $paymentResponse['data']['balance']
//                ,
//                'responseCode' => $paymentResponse['data']['responseCode'],
//                'transactionID' => $paymentResponse['data']['transactionID'],
//                'referenceID' => $paymentResponse['data']['referenceID'],
//                'dateTime' => $paymentResponse['data']['dateTime'],
//            ]);
//
//            $installments = $loanApplication->getLatestInstallment->details;
//
//            if ($installments->isEmpty()) {
//                throw new \Exception('No installments found for this loan application.');
//            }
//
//            $startDate = Carbon::now();
//
//            foreach ($installments as $installment) {
//                $dueDate = $startDate->copy()->addMonths(1);
//
//                $installment->update([
//                    'issue_date' => $startDate,
//                    'due_date' => $dueDate,
//                ]);
//
//                $startDate = $dueDate->copy()->addDay();
//            }
//
//            DB::commit();
//
//            return redirect()->route('show-installment')->with('success', 'Transaction and installments updated successfully.');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
//        }
//    }




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
                'referenceId' => 'money_' . uniqid('', true),
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
                'transactionID' => uniqid('', true),
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

    public function getJSBankStatusDescription(string $responseCode)
    {
        $statuses = [
            '000' => 'Success Response',
            '001' => 'User Id and password should not be empty.',
            '002' => 'Invalid user Id or password.',
            '003' => 'Invalid User Id',
            '004' => 'Invalid request packet',
            '005' => 'Max Length error of Parameters',
            '006' => 'Company not belongs to this User.',
            '007' => 'Product code should not be empty',
            '008' => 'Invalid Company/Product or arrangement not authorized',
            '009' => 'This Product is not allowed to use this API',
            '010' => 'Product is not authorized or not available',
            '011' => 'Bene Contact No. should not be empty',
            '012' => 'Bene Address should not be empty',
            '013' => 'Bene CNIC should not be empty',
            '014' => 'Bene CNIC No should be 13 digits',
            '015' => 'IBAN/Account No. should not be empty',
            '016' => 'Customer/Beneficiary Name should not be empty',
        ];

        return isset($statuses[$responseCode]) ? $statuses[$responseCode] : 'Unknown response code.';
    }


}
