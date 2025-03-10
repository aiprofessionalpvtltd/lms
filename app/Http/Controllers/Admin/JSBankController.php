<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SecurityHelper;
use App\Http\Controllers\Controller;

use App\Models\Bank;
use App\Models\Expense;
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

    public function index()
    {
        $title = 'JS Zindagi API';
        return view('admin.js_bank.index', compact('title'));
    }

    public function resetAuth()
    {
        $response = $this->resetJSBankAuthorization();

        dd($response);
        if ($response['success']) {
            return redirect()->route('jszindagi.index')->with('success', 'Authorization reset successfully.');
        }

        return back()->with('error', 'Failed to reset authorization.');
    }

    public function resetJSBankAuthorization()
    {
        $url = $this->apiUrl . 'client/reset-oauth-blb';
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
                    $envUpdated = $this->updateEnvFile('JS_ZINDAGI_CLIENT_SECRET', $newClientSecret);

                    if ($envUpdated) {
                        return [
                            'success' => true,
                            'message' => 'Authorization reset successfully.',
                            'clientSecret' => $newClientSecret,
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Failed to update environment variables.',
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => $responseData['message'] ?? 'Reset failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to reset authorization with JS Bank API',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while resetting authorization.',
                'error' => $e->getMessage(),
            ];
        }
    }

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

            // Update value in runtime without clearing cache
            config()->set($key, $value);

            return true;
        }

        return false;
    }


    public function generateMpin(Request $request)
    {
        $mpinData = SecurityHelper::generateEncryptedMPIN();

        // Save encrypted MPIN in .env file
        $this->updateEnvFile('JS_ZINDAGI_ENCRYPTED_MPIN', $mpinData['encrypted_mpin']);
        $this->updateEnvFile('JS_ZINDAGI_MPIN', $mpinData['mpin']);

        return redirect()->back()->with('success', 'MPIN generated successfully!');
    }

    public function getJSBankAuthorization()
    {
        $url = $this->apiUrl . 'client/oauth-blb';

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
            CURLOPT_URL => $this->apiUrl . 'api/v2/verifyacclinkacc-blb',
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
            return redirect()->back()->with('error', 'cURL Error: ' . $error);

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
            CURLOPT_URL => $this->apiUrl . 'api/v2/accountopening-blb',
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


    public function walletToWalletInquiry(Request $request)
    {
        $request->validate(['loan_application_id' => 'required|exists:loan_applications,id']);

        DB::beginTransaction();
        try {
            // Fetch loan application details
            $loanApplication = LoanApplication::with('getLatestInstallment.details', 'user.bank_account')
                ->findOrFail($request->loan_application_id);
            $disburseAmount = $loanApplication->loan_amount - $loanApplication->getLatestInstallment->processing_fee;


            // Prepare API request payload
            $payload = json_encode([
                "w2wpiRequest" => [
                    "MerchantType" => $this->merchanType,
                    "TraceNo" => (string)mt_rand(100000, 999999),
                    "CompanyName" => env('JS_ZINDAGI_COMPANY_NAME'),
                    "DateTime" => now()->format('YmdHis'), // Current date-time
                    "TerminalId" => env('JS_ZINDAGI_COMPANY_NAME'),
                    "ReceiverMobileNumber" => $loanApplication->user->profile->mobile_no,
                    "MobileNo" => env('JS_ZINDAGI_COMPANY_MOBILE'),
                    "Amount" => (string)$disburseAmount,
                    "Reserved1" => "03"
                ]
            ]);

            // cURL Request
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => env('JS_ZINDAGI_API_URL') . '/api/v2/w2wpi-blb',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'clientId: ' . env('JS_ZINDAGI_CLIENT_ID'),
                    'clientSecret: ' . env('JS_ZINDAGI_CLIENT_SECRET'),
                    'organizationId: ' . env('JS_ZINDAGI_ORGANIZATION_ID'),
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Decode JSON response
            $responseData = json_decode($response, true);


            if ($httpStatus === 200 && isset($responseData['w2wpiResponse']['ResponseCode']) && $responseData['w2wpiResponse']['ResponseCode'] === "00") {
                DB::commit();

                // Store response in session for confirmation page
                session(['w2wpi_response' => $responseData['w2wpiResponse']]);

                return redirect()->route('jszindagi.wallet-to-wallet.confirmation', $loanApplication->id);
            }

            DB::rollBack();
            return back()->withErrors(['error' => 'Transaction failed: ' . ($responseData['w2wpiResponse']['ResponseDetails'][0] ?? 'Unknown error')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function walletToWalletConfirmation($id)
    {
        $responseData = session('w2wpi_response');
        $title = 'Transaction Confirmation';
        if (!$responseData) {
            return redirect()->route('home')->withErrors(['error' => 'No transaction data found.']);
        }

        return view('admin.js_bank.confirmation', compact('responseData', 'title', 'id'));
    }

    public function confirmWalletTransaction(Request $request)
    {

        // Retrieve response data from session
        $responseData = session('w2wpi_response');

        if (!$responseData) {
            return redirect()->route('home')->withErrors(['error' => 'No transaction data found.']);
        }

        // Generate Trace Number
        $traceNo = (string)mt_rand(100000, 999999);

        // Prepare API request payload
        $payloadArray = [
            "w2wpRequest" => [
                "MerchantType" => $this->merchanType,
                "TraceNo" => $traceNo,
                "CompanyName" => env('JS_ZINDAGI_COMPANY_NAME'),
                "DateTime" => now()->format('YmdHis'),
                "TerminalId" => env('JS_ZINDAGI_COMPANY_NAME'),
                "ReceiverMobileNumber" => $responseData['ReceiverMobileNumber'],
                "MobileNo" => $responseData['CustomerMobile'],
                "Amount" => $responseData['TransactionAmount'],
                "Reserved1" => "03",
                "OtpPin" => env('JS_ZINDAGI_ENCRYPTED_MPIN')
            ]
        ];

        $payloadJson = json_encode($payloadArray);

        // Initialize cURL request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => env('JS_ZINDAGI_API_URL') . '/api/v2/w2wp-blb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payloadJson,
            CURLOPT_HTTPHEADER => [
                'clientId: ' . env('JS_ZINDAGI_CLIENT_ID'),
                'clientSecret: ' . env('JS_ZINDAGI_CLIENT_SECRET'),
                'organizationId: ' . env('JS_ZINDAGI_ORGANIZATION_ID'),
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($curlError) {
            return back()->withErrors(['error' => 'Payment request failed: ' . $curlError]);
        }

        // Decode JSON response
        $responseData = json_decode($response, true);

        // Check for successful response
        if ($httpStatus === 200 && isset($responseData['w2wpResponse']['ResponseCode']) && $responseData['w2wpResponse']['ResponseCode'] === "00") {
            DB::beginTransaction();

            try {
                $transactionData = [
                    'loan_application_id' => $request->id,
                    'user_id' => Auth::id(),
                    'amount' => (float)$payloadArray['w2wpRequest']['Amount'],
                    'payment_method' => 'JS Zindagi Wallet TO Wallet',
                    'status' => 'completed',
                    'transaction_reference' => $traceNo,
                    'remarks' => $responseData['w2wpResponse']['ResponseDetails'][0] ?? 'Transaction Successful',
                    'responseCode' => $responseData['w2wpResponse']['ResponseCode'],
                    'transactionID' => $traceNo,
                    'referenceID' => $traceNo,
                    'dateTime' => currentDateTimeInsert(),
                ];


                // Create transaction record
                Transaction::create($transactionData);

                DB::commit();

                // Clear session data
                session()->forget('w2wpi_response');

                return redirect()->route('show-installment')->with('success', 'Transaction successful.');
            } catch (\Exception $e) {

                DB::rollBack();
                return back()->withErrors(['error' => 'Transaction failed to save: ' . $e->getMessage()]);
            }
        }

        // Handle failed transactions
        return back()->withErrors(['error' => 'Transaction failed: ' . ($responseData['w2wpResponse']['ResponseDetails'][0] ?? 'Unknown error')]);
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
            '0306' => 'Jazz', '0307' => 'Jazz', '0308' => 'Jazz',
            '0309' => 'Jazz', '0310' => 'Jazz',

            '0320' => 'Warid', '0321' => 'Warid', '0322' => 'Warid',
            '0323' => 'Warid', '0324' => 'Warid', '0325' => 'Warid',
            '0326' => 'Warid', '0327' => 'Warid', '0328' => 'Warid', '0329' => 'Warid',

            '0311' => 'Zong', '0312' => 'Zong', '0313' => 'Zong',
            '0314' => 'Zong', '0315' => 'Zong', '0316' => 'Zong',
            '0317' => 'Zong', '0318' => 'Zong', '0319' => 'Zong',

            '0330' => 'Ufone', '0331' => 'Ufone', '0332' => 'Ufone',
            '0333' => 'Ufone', '0334' => 'Ufone', '0335' => 'Ufone',
            '0336' => 'Ufone', '0337' => 'Ufone', '0338' => 'Ufone', '0339' => 'Ufone',

            '0340' => 'Telenor', '0341' => 'Telenor', '0342' => 'Telenor',
            '0343' => 'Telenor', '0344' => 'Telenor', '0345' => 'Telenor',
            '0346' => 'Telenor', '0347' => 'Telenor', '0348' => 'Telenor', '0349' => 'Telenor',

            '0355' => 'Sco' // Special case for SCOM (Gilgit-Baltistan & AJK)
        ];

        return strtoupper($networks[$prefix]) ?? 'Unknown Network';
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
