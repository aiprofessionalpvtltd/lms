<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserBankAccountResource;
use App\Http\Resources\UserEmploymentResource;
use App\Http\Resources\UserFamilyDependentResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserProfileTrackingResource;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\UserBankAccount;
use App\Models\UserEmployment;
use App\Models\UserFamilyDependent;
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

class UserController extends BaseController
{
    public function userInfo(): JsonResponse
    {
        try {
            // Retrieve the authenticated user
            $user = Auth::user();

            // Check if the user exists
            if (!$user) {
                return $this->sendError('User not found.', ['error' => 'User not found.']);
            }

            // Return the user information using UserResource
            return $this->sendResponse(new UserResource($user), 'User information retrieved successfully.');
        } catch (Exception $e) {
            // Catch any exceptions and return an error response
            return $this->sendError('Failed to retrieve user information.', ['error' => $e->getMessage()]);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        // Get the authenticated user
        $user = Auth::user();
        $userProfile = $user->profile;

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_no' => 'required|string|max:15|unique:user_profiles,cnic_no,' . $userProfile->id,
            'issue_date' => 'required|date',
            'expire_date' => 'required|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15|unique:user_profiles,mobile_no,' . $userProfile->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            // Update the user information
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Handle the profile photo and CNIC uploads
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('profile_photos', 'public');
                $userProfile->photo = $photoPath;
            }

            if ($request->hasFile('cnic')) {
                $cnicPath = $request->file('cnic')->store('cnic_photos', 'public');
                $userProfile->cnic = $cnicPath;
            }

            // Update the user's profile information
            $userProfile->update([
                'cnic_no' => $request->cnic_no,
                'issue_date' => $request->issue_date,
                'expire_date' => $request->expire_date,
                'dob' => $request->dob,
                'mobile_no' => $request->mobile_no,
            ]);

            DB::commit();

            return $this->sendResponse(new UserResource($user), 'Profile updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Profile update failed.', ['error' => $e->getMessage()]);
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

    public function storeUserEmployment(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'employment_status_id' => 'required|exists:employment_statuses,id',
            'income_source_id' => 'required|exists:income_sources,id',
            'current_employer' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'gross_income' => 'nullable|numeric',
            'net_income' => 'nullable|numeric',
            'existing_loans' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $request->merge(['user_id' => $user->id]);

            // Create a new UserEmployment record
            $userEmployment = UserEmployment::create($request->all());

            // Load relationships if needed for the response
            $userEmployment->load('employmentStatus', 'incomeSource', 'user');

            DB::commit();

            // Return the response with UserEmploymentResource
            return $this->sendResponse(new UserEmploymentResource($userEmployment), 'User employment details stored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error storing user employment details.', $e->getMessage());
        }
    }

    public function storeFamilyDependent(Request $request)
    {

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'number_of_dependents' => 'required|integer|min:0',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_employment_details' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $request->merge(['user_id' => $user->id]);

            // Create a new UserFamilyDependent record
            $userFamilyDependent = UserFamilyDependent::create($request->all());

            // Load relationships if needed for the response
            $userFamilyDependent->load('user');

            DB::commit();

            // Return the response with UserFamilyDependentResource
            return $this->sendResponse(new UserFamilyDependentResource($userFamilyDependent), 'Family and dependents information stored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error storing family and dependents information.', $e->getMessage());
        }
    }

}
