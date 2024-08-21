<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
            'photo' => 'required|string',
            'cnic' => 'required|string',
            'cnic_no' => 'required|string|max:15|unique:user_profiles,cnic_no',
            'issue_date' => 'required|date',
            'expire_date' => 'required|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15|unique:user_profiles,mobile_no',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        // Create user
        $input = $request->all();
        $input['password'] = $input['password'];
        $user = User::create($input);

        // Handle the profile photo upload
        $profileData = $request->only(['photo','cnic', 'cnic_no', 'issue_date', 'expire_date', 'dob', 'mobile_no']);

        if ($request->has('photo')) {
            $profileData['photo'] = $this->saveBase64Image($request->photo, 'profile_photos');
        }
        if ($request->has('cnic')) {
            $profileData['cnic'] = $this->saveBase64Image($request->cnic, 'cnic_photos');
        }

        // Link profile to the user
        $user->profile()->create($profileData);

        $success['token'] = $user->createToken('LMS')->accessToken;
        $success['name'] = $user->name;

// Return the response with the UserResource
        return $this->sendResponse([
            'name' => $user->name,
            'token' => $success['token'],
            'user' => new UserResource($user)
        ], 'User registered successfully.');
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
//            $this->sendSmsToUser($user->mobile_no, "Your OTP is: {$otpCode}");

            return $this->sendResponse(['mobile_no' => $request->mobile_no , 'otp' => $otpCode], 'OTP sent to your mobile number.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'mobile_no' => 'required',
            'otp' => 'required|digits:6',
        ]);

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
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['name'] = $user->name;
                $success['user'] = new UserResource($user);

                // Delete the OTP after successful verification
                $otpRecord->delete();

                return $this->sendResponse($success, 'User logged in successfully.');
            } else {
                return $this->sendError('Invalid OTP or expired.', ['error' => 'Invalid OTP or expired.']);
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
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


}
