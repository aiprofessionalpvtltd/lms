<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserBankAccountResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\UserBankAccount;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'confirmation_password' => 'required|same:password',
            'photo' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_no' => 'required|string|max:15|unique:user_profiles,cnic_no',
            'issue_date' => 'required|date',
            'expire_date' => 'required|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15|unique:user_profiles,mobile_no',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            // Create user
            $input = $request->only(['name', 'email', 'password']);
            $input['password'] = $input['password'];
            $user = User::create($input);

            // Handle the profile photo and CNIC upload
            $profileData = $request->only(['cnic_no', 'issue_date', 'expire_date', 'dob', 'mobile_no']);

            if ($request->hasFile('photo')) {
                $profileData['photo'] = $request->file('photo')->store('profile_photos', 'public');
            }

            if ($request->hasFile('cnic')) {
                $profileData['cnic'] = $request->file('cnic')->store('cnic_photos', 'public');
            }

            // Link profile to the user
            $user->profile()->create($profileData);

            // Assign role to the user
            $user->assignRole(Role::where('name', 'Customer')->first());

            // Create access token
//            $success['token'] = $user->createToken('LMS')->accessToken;
            $success['name'] = $user->name;

            DB::commit();

            // Return the response with the UserResource
            return $this->sendResponse([
                'name' => $user->name,
//                'token' => $success['token'],
                'user' => new UserResource($user)
            ], 'User registered successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Registration failed.', ['error' => $e->getMessage()]);
        }
    }


    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'mobile_no' => 'required',
            'password' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Find the UserProfile with the given mobile_no
            $userProfile = UserProfile::where('mobile_no', $request->mobile_no)->first();

            if ($userProfile && Auth::attempt(['id' => $userProfile->user_id, 'password' => $request->password])) {
                $user = $userProfile->user;

                // Generate OTP
                $otpCode = rand(100000, 999999);

                // Store OTP
                Otp::create([
                    'user_id' => $user->id,
                    'otp' => $otpCode,
                    'expires_at' => Carbon::now()->addMinutes(10),
                ]);

                // Send OTP to the user's mobile number
//            $this->sendSmsToUser($userProfile->mobile_no, "Your OTP is: {$otpCode}");

                DB::commit();

                return $this->sendResponse(['mobile_no' => $request->mobile_no, 'otp' => $otpCode], 'OTP sent to your mobile number.');
            } else {
                return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Login failed.', ['error' => $e->getMessage()]);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'mobile_no' => 'required',
            'otp' => 'required|digits:6',
        ]);

        DB::beginTransaction();

        try {
            // Find the UserProfile with the given mobile_no
            $userProfile = UserProfile::where('mobile_no', $request->mobile_no)->first();

            if ($userProfile) {
                $user = $userProfile->user;

                // Verify the OTP
                $otpRecord = Otp::where('user_id', $user->id)
                    ->where('otp', $request->otp)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();

                if ($otpRecord) {
                    // OTP is correct, log the user in
                    $accessToken = $user->createToken('MyApp')->accessToken;
                    $success['token'] = $accessToken;
                    $success['name'] = $user->name;
                    $success['user'] = new UserResource($user);

                    // Delete the OTP after successful verification
                    $otpRecord->delete();

                    DB::commit();

                    return $this->sendResponse($success, 'User logged in successfully.');
                } else {
                    return $this->sendError('Invalid OTP or expired.', ['error' => 'Invalid OTP or expired.']);
                }
            } else {
                return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Verification failed.', ['error' => $e->getMessage()]);
        }
    }

    public function sendSmsToUser($mobile_no, $message)
    {
        // Example using Twilio
        try {
            $client = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $client->messages->create(
                $mobile_no,
                [
                    'from' => env('TWILIO_FROM'),
                    'body' => $message,
                ]
            );
        } catch (\Exception $e) {
            // Handle exception if SMS sending fails
            return $this->sendError('Failed to send SMS.', ['error' => $e->getMessage()]);
        }
    }


    public function storeUserBankAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
             'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:user_bank_accounts,account_number',
            'iban' => 'nullable|string|max:34',
            'swift_code' => 'nullable|string|max:11',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            DB::beginTransaction();

            $bankAccount = UserBankAccount::create([
                'user_id' => auth()->user()->id,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'iban' => $request->iban,
                'swift_code' => $request->swift_code,
            ]);
            DB::commit();

            return $this->sendResponse(new UserBankAccountResource($bankAccount), 'Bank account added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error occurred.', ['error' => $e->getMessage()]);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke the user's current token
            $request->user()->token()->revoke();

            return $this->sendResponse([], 'User logged out successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed.', ['error' => $e->getMessage()]);
        }
    }


}
