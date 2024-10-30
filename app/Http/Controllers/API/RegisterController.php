<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserBankAccountResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserProfileTrackingResource;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\UserBankAccount;
use App\Models\UserProfile;
use App\Models\UserProfileTracking;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            // User validation
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'confirmation_password' => 'required|same:password',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',

            // User Profile validation
            'gender_id' => 'required|exists:genders,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'nationality_id' => 'required|exists:nationalities,id',
            'residence_type_id' => 'required|exists:residence_types,id',
            'residence_duration_id' => 'required|exists:residence_durations,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'photo' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_front' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_back' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_no' => 'required|string|max:15|unique:user_profiles,cnic_no',
            'issue_date' => 'nullable|date',
            'expire_date' => 'nullable|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15',
            'alternate_mobile_no' => 'required|string|max:15',
            'permanent_address' => 'required|string|max:255',
            'current_address' => 'required|string|max:255',
            'current_address_duration' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

         DB::beginTransaction();

        try {
            // Step 1: Create User
            $userInput = $request->only(['name', 'email', 'password' ,'province_id','district_id','city_id']);
            $userInput['password'] = $userInput['password']; // Hash password
            $user = User::create($userInput);

            // Step 2: Handle Profile Image & CNIC upload
            $profileData = $request->only([
                'gender_id', 'marital_status_id', 'nationality_id', 'first_name',
                'last_name', 'father_name', 'cnic_no', 'issue_date', 'expire_date',
                'dob', 'mobile_no', 'alternate_mobile_no', 'permanent_address',
                'current_address', 'current_address_duration' ,'residence_type_id' ,'residence_duration_id'
            ]);

            $profileData['dob'] = dateInsert($request->dob);
            $profileData['issue_date'] = dateInsert($request->issue_date);
            $profileData['expire_date'] = dateInsert($request->expire_date);

            if ($request->hasFile('photo')) {
                $profileData['photo'] = $request->file('photo')->store('profile_photos', 'public');
            }

            if ($request->hasFile('cnic_front')) {
                $profileData['cnic_front'] = $request->file('cnic_front')->store('cnic_photos', 'public');
            }

            if ($request->hasFile('cnic_back')) {
                $profileData['cnic_back'] = $request->file('cnic_back')->store('cnic_photos', 'public');
            }

//            dd($profileData);
            // Step 3: Create User Profile and link to the user
            $user->profile()->create($profileData);

            // Step 4: Create User Profile Tracking
            $userTracking = UserProfileTracking::create([
                'user_id' => $user->id,
                'is_registration' => true,  // Mark registration as complete
                'is_kyc' => false,
                'is_profile' => false,
                'is_reference' => false,
                'is_utility' => false,
                'is_bank_statement' => false,
            ]);

            // Step 5: Assign role to the user
            $user->assignRole(Role::where('name', 'Customer')->first());

            // Eager load relationships
            $user = User::with('profile.gender','profile.maritalStatus','profile.nationality')->findOrFail($user->id);

            $accessToken = $user->createToken('MyApp')->accessToken;

            DB::commit();

            // Step 6: Return success response with UserResource
            return $this->sendResponse([
                'name' => $user->name,
                'user' => new UserResource($user),
                'token' => $accessToken,
                'userTracking'  =>new UserProfileTrackingResource($userTracking)
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

            if ($userProfile) {
                // Check if the provided password is correct
                if (Auth::attempt(['id' => $userProfile->user_id, 'password' => $request->password])) {
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
                    // Password is incorrect
                    return $this->sendError('Unauthorized.', ['error' => 'The password is incorrect.']);
                }
            } else {
                // Mobile number is incorrect
                return $this->sendError('Unauthorized.', ['error' => 'The mobile number is not registered.']);
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
                $userTracking = $user->tracking;

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



    public function changePassword(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
            'confirmation_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->sendError('Current password is incorrect.');
            }

            // Update the user's password
            $user->password = $request->new_password;
            $user->save();

            DB::commit();

            return $this->sendResponse([], 'Password changed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Password change failed.', ['error' => $e->getMessage()]);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'mobile_no' => 'required|string|max:15',
        ]);

        DB::beginTransaction();

        try {
            // Find the UserProfile with the given mobile_no
            $userProfile = UserProfile::where('mobile_no', $request->mobile_no)->first();

            if (!$userProfile) {
                return $this->sendError('Mobile number not found.', ['error' => 'Mobile number not found']);
            }

            // Generate OTP
            $otpCode = rand(100000, 999999);

            // Store OTP
            Otp::create([
                'user_id' => $userProfile->user_id,
                'otp' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            DB::commit();

            // Send OTP to the user's mobile number
            // $this->sendSmsToUser($request->mobile_no, "Your OTP is: {$otpCode}");

            return $this->sendResponse(['mobile_no' => $request->mobile_no, 'otp' => $otpCode], 'OTP sent to your mobile number.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to send OTP.', ['error' => $e->getMessage()]);
        }
    }

    public function verifyOtpAndResetPassword(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'mobile_no' => 'required|string|max:15',
            'otp' => 'required|digits:6',
        ]);

        DB::beginTransaction();

        try {
            // Find the UserProfile with the given mobile_no
            $userProfile = UserProfile::where('mobile_no', $request->mobile_no)->first();

            if (!$userProfile) {
                return $this->sendError('Mobile number not found.', ['error' => 'Mobile number not found']);
            }

            $user = $userProfile->user;

            // Verify the OTP
            $otpRecord = Otp::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$otpRecord) {
                return $this->sendError('Invalid or expired OTP.', ['error' => 'Invalid or expired OTP']);
            }

            // Delete the OTP after successful verification
            $otpRecord->delete();

            DB::commit();

            return $this->sendResponse(new UserResource($user), 'OTP Verified successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to reset password.', ['error' => $e->getMessage()]);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|string|max:15',
            'new_password' => 'required|string|min:8',
            'confirmation_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            // Find the UserProfile with the given mobile_no
            $userProfile = UserProfile::where('mobile_no', $request->mobile_no)->first();

            if (!$userProfile) {
                return $this->sendError('Mobile number not found.', ['error' => 'Mobile number not found']);
            }

            $user = $userProfile->user;

            // Update the user's password
            $user->password = $request->new_password;
            $user->save();

            DB::commit();

            return $this->sendResponse(new UserResource($user), 'Password Reset successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Password change failed.', ['error' => $e->getMessage()]);
        }
    }

}
