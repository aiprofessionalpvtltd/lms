<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\LoanApplication;
use App\Models\Transaction;

class JSBankController extends Controller
{
    private $apiUrl;
    private $clientID;
    private $clientSecret;
    private $organizationId;
    private $companyName;
    private $merchanType;

    public function __construct()
    {
        $this->apiUrl = env('JS_ZINDAGI_API_URL');
        $this->clientID = env('JS_ZINDAGI_CLIENT_ID');
        $this->clientSecret = env('JS_ZINDAGI_CLIENT_SECRET');
        $this->organizationId = env('JS_ZINDAGI_ORGANIZATION_ID');
        $this->companyName = env('JS_ZINDAGI_COMPANY_NAME');
        $this->merchanType = env('JS_ZINDAGI_MERCHANT_TYPE');
    }

    public function getJSBankAuthorization()
    {
        $url = 'https://z-sandbox.jsbl.com/zconnect/client/oauth-blb';

        try {
            $response = Http::withHeaders([
                'clientId' => $this->clientID,
            ])->get($url);

            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['responseCode'] === '0000') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Authorization successful',
                        'data' => $responseData['payLoad'], // Contains clientSecret & organizationId
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $responseData['message'] ?? 'Failed to authenticate',
                    ], 400);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to JS Bank API',
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching authorization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetJSBankAuthorization()
    {
        $url = 'https://z-sandbox.jsbl.com/zconnect/client/reset-oauth-blb';
        $payload = ['clientSecretId' => $this->clientID];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['responseCode'] === '0000') {
                    $newClientSecret = $responseData['payLoad']['clientSecret'];

                    // Update .env file with new clientSecret
                    $this->updateEnvFile('JS_ZINDAGI_CLIENT_SECRET', $newClientSecret);

                    return response()->json([
                        'success' => true,
                        'message' => 'Authorization reset successfully.',
                        'clientSecret' => $newClientSecret,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $responseData['message'] ?? 'Reset failed',
                    ], 400);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset authorization with JS Bank API',
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting authorization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Verify if Account Exists
    public function verifyAccount($id)
    {
         $customer = User::with('roles', 'profile', 'bank_account')->find($id);

        $bodyRequest = [
            "VerifyAccLinkAccRequest" => [
                "MerchantType" => $this->merchanType,
                "TraceNo" => (string)mt_rand(100000, 999999),
                "CNIC" => inputMaskDash($customer->profile->cnic_no),
                "MobileNo" => $customer->profile->mobile_no,
                "DateTime" => now()->format('YmdHis'),
                "CompanyName" => $this->companyName,
                "Reserved1" => "01",
                "Reserved2" => "01",
                "TransactionType" => "02"
            ]
        ];


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . 'verifyacclinkacc-blb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($bodyRequest),
            CURLOPT_HTTPHEADER => [
                'clientId: ' . $this->clientID . '',
                'clientSecret: ' . $this->clientSecret . '',
                'organizationId: ' . $this->organizationId . '',
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'message' => 'cURL Error: ' . $error];
        }

        $responseData = json_decode($response, true);

        if ($httpCode === 200 && isset($responseData['VerifyAccLinkAccResponse'])) {
            $responseCode = $responseData['VerifyAccLinkAccResponse']['ResponseCode'] ?? 'Unknown';

            if ($responseCode === '00') {
                $customer->is_zindagi_verified = true;
                $customer->save();

                return redirect()->back()->with('success', 'Customer Verified Successfully');
            } else {

                return $this->openAccount($id); // Call openAccount if ResponseCode is not 00
            }
        }
        return redirect()->back()->with('error', 'Verification failed or invalid response received');

    }

    public function openAccount($id)
    {
        $customer = User::with('roles', 'profile', 'bank_account')->find($id);

        $bodyRequest = [
            "AccountOpeningRequest" => [
                "MerchantType" => $this->merchanType,
                "TraceNo" => (string)mt_rand(100000, 999999),
                "CNIC" => inputMaskDash($customer->profile->cnic_no),
                "MobileNo" => $customer->profile->mobile_no,
                "CompanyName" => "NOVA",
                "DateTime" => now()->format('YmdHis'),
                "CnicIssuanceDate" => date('Ymd', strtotime($customer->profile->issue_date)),
                "MobileNetwork" => $this->getMobileNetwork($customer->profile->mobile_no),
                "EmailId" => $customer->email
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . 'accountopening-blb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($bodyRequest),
            CURLOPT_HTTPHEADER => [
                'clientId: ' . $this->clientID . '',
                'clientSecret: ' . $this->clientSecret . '',
                'organizationId: ' . $this->organizationId . '',
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'message' => 'cURL Error: ' . $error];
        }

        $responseData = json_decode($response, true);

        if ($httpCode === 200 && isset($responseData['AccountOpeningResponse'])) {
            $responseCode = $responseData['AccountOpeningResponse']['ResponseCode'] ?? 'Unknown';

            if ($responseCode === '00') {
                $customer->is_zindagi_verified = true;
                $customer->is_account_opened = true;
                $customer->account_opening_date = currentDateInsert();
                $customer->zindagi_trace_no = $responseData['AccountOpeningResponse']['TraceNo'];
                $customer->save();
                return redirect()->back()->with('success', 'Customer Account Opened Successfully');

            } else {
                $responseDetail = $responseData['AccountOpeningResponse']['ResponseDetails'];
                return redirect()->back()->with('error', $responseDetail);
            }
        } else {
            $errorMessage = $responseData['messages'];
            return redirect()->back()->with('error', $errorMessage);

        }
    }

    // Handle Wallet Transaction
    public function handleWalletTransaction(Request $request)
    {
        $request->validate(['loan_application_id' => 'required|exists:loan_applications,id']);

        DB::beginTransaction();
        try {
            $loanApplication = LoanApplication::with('getLatestInstallment.details', 'user.bank_account')
                ->findOrFail($request->loan_application_id);
            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;
            $userBankDetail = $loanApplication->user->bank_account;

            // Step 1: Verify Account
            $verificationResponse = $this->verifyAccount($request);
            if (!$verificationResponse['success']) {
                throw new \Exception($verificationResponse['message']);
            }

            // Step 2: Open Account if Not Exists
            if (!$verificationResponse['success']) {
                $openingResponse = $this->openAccount($request);
                if (!$openingResponse['success']) {
                    throw new \Exception($openingResponse['message']);
                }
            }

            // Process Payment
            $paymentResponse = $this->openAccount($request);
            if (!$paymentResponse['success']) {
                throw new \Exception($paymentResponse['message']);
            }

            // Save Transaction
            Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(),
                'amount' => $disburseAmount,
                'payment_method' => 'JS Bank',
                'status' => 'completed',
                'transaction_reference' => $paymentResponse['data']['TraceNo'],
                'remarks' => $paymentResponse['data']['ResponseDetails'][0] ?? '',
                'responseCode' => $paymentResponse['data']['ResponseCode'],
                'referenceID' => $paymentResponse['data']['TraceNo'] ?? '',
                'dateTime' => now(),
            ]);

            DB::commit();
            return redirect()->route('show-installment')->with('success', 'Transaction and installments updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    // Get Response Code Descriptions
    private function getJSBankZindagiStatusDescription($code)
    {
        $statusDescriptions = [
            '00' => 'Transaction successful',
            '01' => 'Invalid credentials',
            '02' => 'Account does not exist',
            '03' => 'Insufficient funds',
            '04' => 'Invalid request parameters',
            '05' => 'Duplicate request',
            '06' => 'Bank system error',
            '07' => 'Account verification failed',
            '08' => 'Account already exists',
            '09' => 'Request timed out',
            '10' => 'Unauthorized access',
            '11' => 'Transaction declined by bank',
            '12' => 'Invalid CNIC or Mobile number',
            '13' => 'Limit exceeded',
            '14' => 'Service unavailable',
            '15' => 'Invalid organization ID',
            '16' => 'Invalid transaction type',
            '99' => 'Unknown error occurred',

            // Newly added response codes
            '4001' => 'Bad Request - Invalid Access Token',
            '4002' => 'Bad Request - Invalid Request Payload',
            '4003' => 'Bad Request - Invalid Authorization Header',
            '4004' => 'Something Went Wrong',
            '4005' => 'Record Not Found',
            '4006' => 'Invalid Client Id/Secret',
            '4007' => 'Bad Request - Invalid Access Token',

        ];

        return $statusDescriptions[$code] ?? 'Unknown response code';
    }

    // Helper function for headers
    private function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'clientID' => $this->clientID,
            'clientSecret' => $this->clientSecret,
            'organizationId' => $this->organizationId,
        ];
    }

    private function getMobileNetwork($mobileNo)
    {
        // Ensure mobile number is in correct format
        $mobileNo = preg_replace('/\D/', '', $mobileNo); // Remove non-numeric characters

        // Check if number has the correct length (without country code)
        if (strlen($mobileNo) === 11) {
            $prefix = substr($mobileNo, 0, 4); // Get first four digits
        } elseif (strlen($mobileNo) === 13 && substr($mobileNo, 0, 2) === "92") {
            $prefix = substr($mobileNo, 2, 4); // Get first four digits after country code
        } else {
            return "Invalid Number";
        }

        // Mapping of prefixes to networks
        $networks = [
            '0300' => 'Jazz', '0301' => 'Jazz', '0302' => 'Jazz',
            '0303' => 'Jazz', '0304' => 'Jazz', '0305' => 'Jazz',
            '0306' => 'Jazz', '0307' => 'Jazz',

            '0320' => 'Zong', '0321' => 'Zong', '0322' => 'Zong',
            '0323' => 'Zong', '0324' => 'Zong', '0325' => 'Zong',

            '0330' => 'Ufone', '0331' => 'Ufone', '0332' => 'Ufone',
            '0333' => 'Ufone', '0334' => 'Ufone', '0335' => 'Ufone',
            '0336' => 'Ufone', '0337' => 'Ufone',

            '0340' => 'Telenor', '0341' => 'Telenor', '0342' => 'Telenor',
            '0343' => 'Telenor', '0344' => 'Telenor', '0345' => 'Telenor',
            '0346' => 'Telenor', '0347' => 'Telenor',

            '0355' => 'Sco' // Special case for SCOM (Gilgit-Baltistan & AJK)
        ];

        return strtoupper($networks[$prefix]) ?? 'Unknown Network';
    }

    // Update .env file with new clientSecret
    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $env = file_get_contents($path);
            $pattern = "/^" . preg_quote($key, '/') . "=.*/m";

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, "$key=\"$value\"", $env);
            } else {
                $env .= "\n$key=\"$value\"";
            }

            file_put_contents($path, $env);

            // Refresh environment variables
            Artisan::call('config:clear');
            Artisan::call('config:cache');
        }
    }


//=========================================================================================


    public function JSBankPaymentIBFT(string $accessToken, array $paymentData)
    {
        $url = 'https://connect.jsbl.com/JSQuickPayAPI/PaymentTrans';
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        try {
            $response = Http::withHeaders($headers)->post($url, $paymentData);
            $responseData = $response->json();

            if ($responseData['ResponseCode'] === '000') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully.',
                    'data' => $responseData,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment.',
                'error' => $responseData,
            ], $response->status());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function JSBankIBFTAPI(Request $request)
    {
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
        ]);

        DB::beginTransaction();

        try {
            $request->merge(['payment_method' => 'JS Bank']);
            $loanApplication = LoanApplication::with('getLatestInstallment.details', 'user.bank_account')
                ->findOrFail($request->loan_application_id);

            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;
            $userBankDetail = $loanApplication->user->bank_account;
            $userDetail = $loanApplication->user;
            $customerBank = Bank::where('name', $userBankDetail->bank_name)->first();

            $tokenResponse = $this->getTokenJSBank()->getData(true);

            if (!$tokenResponse['success']) {
                throw new \Exception($tokenResponse['message']);
            }

            $accessToken = $tokenResponse['data'];
            $transactionRequests = [
                'js_bank_ibft' => [
                    "MethodName" => "TRANS",
                    "CompanyCode" => "SMPL",
                    "ProductCode" => "IBFT",
                    "CustomerRefNo" => substr($loanApplication->application_id . '-JS', 0, 17),
                    "DebitAccount" => "0002369168",
                    "BeneAccountNo" => $userBankDetail->iban,
                    "Amount" => (int)$disburseAmount,
                    "BeneName" => $userBankDetail->account_name,
                    "CustomerName" => $userDetail->name,
                    "BankCode" => $customerBank->code
                ],
                'js_bank_ift' => [
                    "MethodName" => "TRANS",
                    "CompanyCode" => "SMPL",
                    "ProductCode" => "IFT",
                    "CustomerRefNo" => substr($loanApplication->application_id . '-JS', 0, 17),
                    "DebitAccount" => "0002369168",
                    "BeneAccountNo" => $userBankDetail->iban,
                    "Amount" => (int)$disburseAmount,
                    "BeneName" => $userBankDetail->account_name,
                    "CustomerName" => $userDetail->name,
                ]
            ];

            if (isset($transactionRequests[$request->service_api])) {
                $paymentData['TransactionRequest'] = $transactionRequests[$request->service_api];
            }

            $paymentResponse = $this->JSBankPaymentIBFT($accessToken, $paymentData)->getData(true);

            if (!$paymentResponse['success']) {
                return redirect()->route('disbursement.show', $loanApplication->id)
                    ->with('error', $paymentResponse['message']);
            }

            Transaction::create([
                'loan_application_id' => $loanApplication->id,
                'user_id' => Auth::id(),
                'amount' => $paymentData['TransactionRequest']['Amount'],
                'payment_method' => 'JS Bank',
                'status' => 'completed',
                'transaction_reference' => $paymentData['TransactionRequest']['CustomerRefNo'],
                'remarks' => $paymentResponse['data']['ResponseMessage'],
                'responseCode' => $paymentResponse['data']['ResponseCode'],
                'referenceID' => $paymentData['TransactionRequest']['CustomerRefNo'] ?? '',
                'dateTime' => now(),
            ]);

            DB::commit();
            return redirect()->route('show-installment')->with('success', 'Transaction successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getTokenJSBank()
    {
        $url = 'https://connect.jsbl.com/JSQuickPayAPI/gettoken';
        $userID = 'AKHTARSMPL';
        $userPassword = 'Sarmaya@2025';

        $encodedUserID = base64_encode($userID);
        $encodedPassword = base64_encode($userPassword);

        try {
            $response = Http::withHeaders([
                'UserID' => $encodedUserID,
                'Password' => $encodedPassword,
            ])->get($url);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'success' => true,
                    'message' => 'Token retrieved successfully.',
                    'data' => $responseData['data'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve token.',
                    'error_code' => $response->status(),
                ], $response->status());
            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the token.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


}
