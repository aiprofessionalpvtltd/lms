<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
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
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
