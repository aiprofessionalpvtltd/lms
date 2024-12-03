<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserBankAccountResource;
use App\Http\Resources\UserEducationResource;
use App\Http\Resources\UserEmploymentResource;
use App\Http\Resources\UserFamilyDependentResource;
use App\Http\Resources\UserGuarantorResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserProfileTrackingResource;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\UserBankAccount;
use App\Models\UserEducation;
use App\Models\UserEmployment;
use App\Models\UserFamilyDependent;
use App\Models\UserGuarantor;
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

            $user->load('profile.gender' ,'profile.maritalStatus' ,'profile.nationality' );

            // Return the user information using UserResource
            return $this->sendResponse(new UserResource($user), 'User information retrieved successfully.');
        } catch (Exception $e) {
            // Catch any exceptions and return an error response
            return $this->sendError('Failed to retrieve user information.', ['error' => $e->getMessage()]);
        }
    }

    public function getTracking(): JsonResponse
    {
        try {
            // Retrieve the authenticated user
            $user = Auth::user();

            // Check if the user exists
            if (!$user) {
                return $this->sendError('User not found.', ['error' => 'User not found.']);
            }

            // Return the user information using UserResource
            return $this->sendResponse(new UserProfileTrackingResource($user->tracking), 'User Tracking retrieved successfully.');
        } catch (Exception $e) {
            // Catch any exceptions and return an error response
            return $this->sendError('Failed to retrieve user information.', ['error' => $e->getMessage()]);
        }
    }

    public function updateProfile(Request $request): \Illuminate\Http\Response
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

            // Check if the user is at least 21 years old
            $dob = \Carbon\Carbon::parse($request->dob);
            if ($dob->age < 21) {
                return $this->sendError('Date Of Birth Validation.', 'The user must be at least 21 years old.');
             }


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
            'employment_duration' => 'nullable|string|max:255',
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
            UserEmployment::create($request->all());

            $user->load('tracking', 'familyDependent', 'bank_account', 'profile', 'education','employment','references');

            $user->tracking->update(['is_kyc' => 1]);

            DB::commit();

            // Return the response with UserResource
            return $this->sendResponse(new UserResource($user), 'User KYC details stored successfully.');

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

    public function storeUserGuarantor(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'guarantor_contact_name' => 'required|string|max:255',
            'relationship_id' => 'required|exists:relationships,id',
            'guarantor_contact_number' => 'nullable|string|max:15',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Check if the user already has 4 guarantors
            $existingGuarantorsCount = $user->references()->count();
            if ($existingGuarantorsCount >= 4) {
                return $this->sendError('You cannot add more than 4 guarantors.');
            }

            // Create a new UserGuarantor record for the authenticated user
           UserGuarantor::create([
                'user_id' => $user->id,  // Get the authenticated user's ID
                'guarantor_contact_name' => $request->guarantor_contact_name,
                'relationship_id' => $request->relationship_id,
                'guarantor_contact_number' => $request->guarantor_contact_number,
            ]);


            $user->load('tracking', 'familyDependent', 'bank_account', 'profile', 'education','employment','references');

            // Reload the references to get the updated count
            $totalGuarantors = $user->references()->count();

            // If the user now has exactly 4 guarantors, update the tracking
            if ($totalGuarantors == 2) {
                $user->tracking->update(['is_reference' => 1]);
            }


            DB::commit();

            // Return the response with the GuarantorResource
            return $this->sendResponse(['references' => UserGuarantorResource::collection($user->references) , 'total_counts' => count($user->references)], 'Reference Guarantor added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Guarantor creation failed.', ['error' => $e->getMessage()]);
        }
    }

    public function storeUserEducation(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'education_id' => 'required|exists:educations,id', // Ensure valid education
            'university_name' => 'required|string|max:255',
        ]);

        try {
            // Transaction to ensure atomicity with Eloquent
            DB::beginTransaction();

            $validatedData['user_id'] = Auth::id();

            // Create UserEducation using Eloquent
            $userEducation = UserEducation::create($validatedData);

            DB::commit();

            return $this->sendResponse(new UserEducationResource($userEducation), 'User Education added successfully.');


        } catch (\Exception $e) {
            // Handle exception and rollback
            DB::rollBack();

            return $this->sendError('User Education creation failed.', ['error' => $e->getMessage()]);

        }
    }

    public function storeProfile(Request $request)
    {

        // Validate the input for all three operations
        $validator = Validator::make($request->all(), [
            // Validation for Family Dependents
            'number_of_dependents' => 'required|integer|min:0',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_employment_details' => 'nullable|string',

            // Validation for Bank Account
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:user_bank_accounts,account_number',
            'iban' => 'nullable|string|max:34',
            'swift_code' => 'nullable|string|max:11',

            // Validation for Education
            'education_id' => 'required|exists:educations,id',
            'university_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Store Family Dependents
            $request->merge(['user_id' => $user->id]);
            UserFamilyDependent::create([
                'user_id' => $user->id,
                'number_of_dependents' => $request->number_of_dependents,
                'spouse_name' => $request->spouse_name,
                'spouse_employment_details' => $request->spouse_employment_details,
            ]);

            // Store Bank Account
            UserBankAccount::create([
                'user_id' => $user->id,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'iban' => $request->iban,
                'swift_code' => $request->swift_code,
            ]);

            // Store Education
            UserEducation::create([
                'user_id' => $user->id,
                'education_id' => $request->education_id,
                'university_name' => $request->university_name,
            ]);

            $user->load('tracking', 'familyDependent', 'bank_account', 'profile', 'education','employment','references');

            $user->tracking->update(['is_profile' => 1]);

            DB::commit();

            // Return the response with all resources
            return $this->sendResponse([
                'user' => new UserResource($user),

            ], 'Profile data stored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error occurred.', $e->getMessage());
        }
    }


}
