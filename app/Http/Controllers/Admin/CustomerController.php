<?php

namespace App\Http\Controllers\Admin;


use App\Helpers\LogActivity;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Bank;
use App\Models\City;
use App\Models\District;
use App\Models\Education;
use App\Models\EmploymentStatus;
use App\Models\ExistingLoan;
use App\Models\Gender;
use App\Models\IncomeSource;
use App\Models\Installment;
use App\Models\JobTitle;
use App\Models\LoanApplication;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\Province;
use App\Models\Relationship;
use App\Models\ResidenceDuration;
use App\Models\ResidenceType;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserEducation;
use App\Models\UserEmployment;
use App\Models\UserFamilyDependent;
use App\Models\UserGuarantor;
use App\Models\UserProfileTracking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;


class CustomerController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-customer'])->only(['index', 'show']);
        $this->middleware(['permission:edit-customer'])->only(['edit', 'update', 'resetID', 'changePassword', 'change']);
        $this->middleware(['permission:create-customer'])->only(['create', 'store']);
        $this->middleware(['permission:delete-customer'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $title = 'All Customers';

        if ($request->ajax()) {
            $customers = User::with(['roles', 'profile', 'tracking'])
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Customer');
                })
                ->orderBy('created_at', 'DESC');

            return DataTables::of($customers)
                ->addColumn('phone_no', function ($customer) {
                    return $customer->profile->mobile_no ?? '';
                })
                ->addColumn('gender', function ($customer) {
                    return $customer->profile->gender->name ?? '';
                })
                ->addColumn('cnic', function ($customer) {
                    return $customer->profile->cnic_no ?? '';
                })
                ->addColumn('province', function ($customer) {
                    return $customer->province->name ?? '';
                })
                ->addColumn('district', function ($customer) {
                    return $customer->district->name ?? '';
                })
                ->addColumn('city', function ($customer) {
                    return $customer->city->name ?? '';
                })
                ->addColumn('score_level', function ($customer) {
                    return $customer->tracking->score ?? 0;
                })
                ->addColumn('risk_assessment', function ($customer) {
                    $score = $customer->tracking->score ?? 0;
                    $riskAssessment = $this->determineRiskLevel($score);
                    return '<span title="' . $riskAssessment['loan_eligibility'] . '">' . $riskAssessment['risk_level'] . '</span>';
                })
                ->addColumn('is_nacta_clear', function ($customer) {
                    $status = $customer->is_nacta_clear ?? 0;
                    $class = $status == 1 ? 'text-success' : 'text-danger';
                    $label = $status == 1 ? 'Clear' : 'Not Clear';
                    return '<span class="' . $class . '">' . $label . '</span>';
                })

                ->addColumn('actions', function ($customer) {
                    $actions = '';
                    $actions .= '<a title="View Profile" href="' . route('view-customer-profile', $customer->id) . '" class="text-primary me-3"><i class="fas fa-user-astronaut"></i></a><br>';
                    if (auth()->user()->can('view-customer')) {
                        $actions .= '<a title="View" href="' . route('view-customer', $customer->id) . '" class="text-primary me-3"><i class="fas fa-eye"></i></a>';
                        $actions .= '<a title="Edit" href="' . route('edit-customer', $customer->id) . '" class="text-success me-3"><i class="fas fa-edit"></i></a>';
                    }
                    return '<div class="d-flex">' . $actions . '</div>';
                })
                ->rawColumns(['risk_assessment', 'actions' ,'is_nacta_clear'])
                ->make(true);
        }

        LogActivity::addToLog('Customer Listing View');

        return view('admin.customer.index', compact('title'));
    }

    public function view($id)
    {
        $title = 'Edit User';
        $customer = User::with('roles', 'profile', 'bank_account', 'tracking',
            'employment.employmentStatus', 'employment.incomeSource', 'employment.existingLoan',
            'familyDependent', 'education.education', 'references.relationship')
            ->find($id);


        $this->calculateUserScore($customer);
        LogActivity::addToLog('Customer ID : '.$id.' View');

        return view('admin.customer.view', compact('title', 'customer'));
    }

    public function profile($id)
    {
        $title = 'User Profie';
        $customer = User::with('roles', 'profile', 'bank_account', 'tracking',
            'employment.employmentStatus', 'employment.incomeSource', 'employment.existingLoan',
            'familyDependent', 'education.education', 'references.relationship')
            ->find($id);


        try {
            // Fetch loan applications based on the status
            $loanApplications = LoanApplication::where('user_id',$id)->get();
            $installments = Installment::with('details')->where('user_id',$id)->get();

//            dd($loanApplications);


        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Application Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError($e->getMessage());
        }
        return view('admin.customer.profile', compact('title', 'customer' ,'loanApplications','installments'));
    }


    public function index()
    {
        $title = 'Customer';
        $genders = Gender::all();
        $provinces = Province::all();
        $cities = City::all();
        $districts = District::all();
        $maritalStatuses = MaritalStatus::all();
        $nationalities = Nationality::all();
        $residenceTypes = ResidenceType::all();
        $residenceDurations = ResidenceDuration::all();
        $employmentStatus = EmploymentStatus::all();
        $incomeSources = IncomeSource::all();
        $existingLoan = ExistingLoan::all();
        $relationships = Relationship::all();
        $educations = Education::all();
        $jobs = JobTitle::all();

        $banks = Bank::all();
        return view('admin.customer.create',
            compact('title',
                'genders', 'provinces', 'cities', 'districts',
                'maritalStatuses', 'nationalities', 'residenceTypes',
                'residenceDurations', 'banks', 'employmentStatus', 'incomeSources'
                , 'existingLoan', 'relationships', 'educations', 'jobs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User validation
            'email' => 'required|email|unique:users,email',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'is_nacta_clear' => 'required',

            // User Profile validation
            'gender_id' => 'required|exists:genders,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'nationality_id' => 'required|exists:nationalities,id',
            'residence_type_id' => 'required|exists:residence_types,id',
            'residence_duration_id' => 'required|exists:residence_durations,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_front' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_back' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_no' => 'required|string|max:15|unique:user_profiles,cnic_no',
            'issue_date' => 'required|date',
            'expire_date' => 'required|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15',
            'alternate_mobile_no' => 'required|string|max:15',
            'permanent_address' => 'required|string|max:255',
            'current_address' => 'required|string|max:255',
            'current_address_duration' => 'required|string|max:255',


            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:user_bank_accounts,account_number',
            'iban' => 'required|string|max:34',
            'swift_code' => 'required|numeric',

            'employment_status_id' => 'required|exists:employment_statuses,id',
            'income_source_id' => 'required|exists:income_sources,id',
            'current_employer' => 'nullable|string|max:255',
            'employment_duration' => 'nullable|string|max:255',
            'job_title_id' => 'required|string|max:255',
            'gross_income' => 'required|numeric',
            'net_income' => 'required|numeric',
            'existing_loans_id' => 'required|string',

            'number_of_dependents' => 'required|integer|min:0',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_employment_details' => 'nullable|string',


            'education_id' => 'required|exists:educations,id', // Ensure valid education
            'university_name' => 'required|string|max:255',
        ]);

//        dd($validator->errors());
        if ($validator->fails()) {
//            dd($validator->errors());
            return redirect()->back()
                ->withErrors($validator->errors())
                ->withInput();
        }


        DB::beginTransaction();

        try {



            // Step 1: Create User
            $userInput = $request->only(['email', 'province_id', 'district_id', 'city_id']);
            $userInput['name'] = $request->first_name . ' ' . $request->last_name;
            $userInput['password'] = 123456; // Hash password
            $userInput['is_nacta_clear'] = $request->is_nacta_clear; // Hash password
            $user = User::create($userInput);

            $request->merge(['user_id' => $user->id]);


            // Step 2: Handle Profile Image & CNIC upload
            $profileData = $request->only([
                'gender_id', 'marital_status_id', 'nationality_id', 'first_name',
                'last_name', 'father_name', 'cnic_no', 'issue_date', 'expire_date',
                'dob', 'mobile_no', 'alternate_mobile_no', 'permanent_address',
                'current_address', 'current_address_duration', 'residence_type_id', 'residence_duration_id'
            ]);

            $profileData['dob'] = dateInsert($request->dob);
            $profileData['issue_date'] = dateInsert($request->issue_date);
            $profileData['expire_date'] = dateInsert($request->expire_date);
            $profileData['mobile_no'] = inputMaskDash($request->mobile_no);
            $profileData['alternate_mobile_no'] = inputMaskDash($request->alternate_mobile_no);

            if ($request->hasFile('photo')) {
                $profileData['photo'] = $request->file('photo')->store('profile_photos', 'public');
            }

            if ($request->hasFile('cnic_front')) {
                $profileData['cnic_front'] = $request->file('cnic_front')->store('cnic_photos', 'public');
            }

            if ($request->hasFile('cnic_back')) {
                $profileData['cnic_back'] = $request->file('cnic_back')->store('cnic_photos', 'public');
            }

            // Step 3: Create User Profile and link to the user
            $user->profile()->create($profileData);


            // Step 5: Assign role to the user
            $user->assignRole(Role::where('name', 'Customer')->first());


            // Store education Data
            $userEducation = UserEducation::create($request->all());


            // Create a new UserFamilyDependent record
            $userFamilyDependent = UserFamilyDependent::create($request->all());

            // Create a new UserEmployment record
            $userEmployment = UserEmployment::create($request->all());

            // Create a new Bank Information
            $bankAccount = UserBankAccount::create($request->all());

             if(isset($request->relationship_id )){
                foreach ($request->relationship_id as $key => $relationshipId) {
                    $userGuarantor = [
                        'user_id' => $user->id,
                        'relationship_id' => $relationshipId,
                        'guarantor_contact_name' => $request->guarantor_contact_name[$key],
                        'guarantor_contact_number' => $request->guarantor_contact_number[$key],
                    ];

                    // Create a new UserGuarantor record
                    UserGuarantor::create($userGuarantor);
                }
            }





            //Last Step: Create User Profile Tracking
            $userTracking = UserProfileTracking::create([
                'user_id' => $user->id,
                'is_registration' => true,  // Mark registration as complete
                'is_kyc' => true,
                'is_profile' => true,
                'is_reference' => true,
                'is_utility' => true,
                'is_bank_statement' => true,
            ]);


            $user->load('tracking', 'familyDependent', 'bank_account', 'profile', 'education', 'employment', 'references');

            $this->calculateUserScore($user);

            LogActivity::addToLog('Customer  : '.$userInput['name'].' Created');

            DB::commit();

            return redirect()->route('show-customer')->with('success', 'Customer Created Successfully');


        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('show-customer')->with('error', $e->getMessage());
        }


    }

    public function edit($id)
    {
        $title = 'Edit Customer';
        $customer = User::with(['profile', 'familyDependent', 'bank_account', 'education', 'employment', 'references', 'tracking'])
            ->findOrFail($id);

        $genders = Gender::all();
        $provinces = Province::all();
        $cities = City::all();
        $districts = District::all();
        $maritalStatuses = MaritalStatus::all();
        $nationalities = Nationality::all();
        $residenceTypes = ResidenceType::all();
        $residenceDurations = ResidenceDuration::all();
        $employmentStatus = EmploymentStatus::all();
        $incomeSources = IncomeSource::all();
        $existingLoan = ExistingLoan::all();
        $relationships = Relationship::all();
        $educations = Education::all();
        $jobs = JobTitle::all();
        $banks = Bank::all();

//        dd($customer);
        return view('admin.customer.edit', compact(
            'title', 'customer', 'genders', 'provinces', 'cities', 'districts',
            'maritalStatuses', 'nationalities', 'residenceTypes', 'residenceDurations',
            'banks', 'employmentStatus', 'incomeSources', 'existingLoan',
            'relationships', 'educations', 'jobs'
        ));
    }

    public function update(Request $request, $id)
    {
        $customer = User::with(['profile', 'familyDependent', 'bank_account', 'education', 'employment', 'references'])
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            // Add the same validation rules as in the store method, but handle `unique` constraints correctly
            'email' => 'required|email|unique:users,email,' . $customer->id,
             'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'is_nacta_clear' => 'required',

            // User Profile validation
            'gender_id' => 'required|exists:genders,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'nationality_id' => 'required|exists:nationalities,id',
            'residence_type_id' => 'required|exists:residence_types,id',
            'residence_duration_id' => 'required|exists:residence_durations,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_front' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_back' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cnic_no' => 'required|string|max:15',
            'issue_date' => 'required|date',
            'expire_date' => 'required|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15',
            'alternate_mobile_no' => 'required|string|max:15',
            'permanent_address' => 'required|string|max:255',
            'current_address' => 'required|string|max:255',
            'current_address_duration' => 'required|string|max:255',


            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'swift_code' => 'required|numeric',

            'employment_status_id' => 'required|exists:employment_statuses,id',
            'income_source_id' => 'required|exists:income_sources,id',
            'current_employer' => 'nullable|string|max:255',
            'employment_duration' => 'nullable|string|max:255',
            'job_title_id' => 'required|string|max:255',
            'gross_income' => 'required|numeric',
            'net_income' => 'required|numeric',
            'existing_loans_id' => 'required|string',

            'number_of_dependents' => 'required|integer|min:0',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_employment_details' => 'nullable|string',

//            'guarantor_contact_name' => 'nullable',
//            'relationship_id' => 'nullable|exists:relationships,id',
//            'guarantor_contact_number' => 'nullable',

            'education_id' => 'required|exists:educations,id', // Ensure valid education
            'university_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors())
                ->withInput();
        }

        DB::beginTransaction();

        try {

            $request->merge(['name' => $request->first_name . ' ' . $request->last_name]);

            // Step 1: Update User Basic Data
            $customer->update($request->only(['name','email', 'province_id', 'district_id', 'city_id' ,'is_nacta_clear']));

            // Step 2: Update User Profile
            $profileData = $request->only([
                'gender_id', 'marital_status_id', 'nationality_id', 'first_name',
                'last_name', 'father_name', 'cnic_no', 'issue_date', 'expire_date',
                'dob', 'mobile_no', 'alternate_mobile_no', 'permanent_address',
                'current_address', 'current_address_duration', 'residence_type_id', 'residence_duration_id'
            ]);
            $profileData['mobile_no'] = inputMaskDash($request->mobile_no);
            $profileData['alternate_mobile_no'] = inputMaskDash($request->alternate_mobile_no);
            if ($request->hasFile('photo')) {
                // Delete the old file if it exists
                if ($customer->profile->photo) {
                    Storage::disk('public')->delete($customer->profile->photo);
                }
                // Store the new file
                $profileData['photo'] = $request->file('photo')->store('profile_photos', 'public');
            }

            if ($request->hasFile('cnic_front')) {
                // Delete the old file if it exists
                if ($customer->profile->cnic_front) {
                    Storage::disk('public')->delete($customer->profile->cnic_front);
                }
                // Store the new file
                $profileData['cnic_front'] = $request->file('cnic_front')->store('cnic_photos', 'public');
            }

            if ($request->hasFile('cnic_back')) {
                // Delete the old file if it exists
                if ($customer->profile->cnic_back) {
                    Storage::disk('public')->delete($customer->profile->cnic_back);
                }
                // Store the new file
                $profileData['cnic_back'] = $request->file('cnic_back')->store('cnic_photos', 'public');
            }


            $customer->profile()->update($profileData);

            // Step 3: Update Other Relations (Family Dependent, Employment, Bank Account, etc.)
            $customer->familyDependent()->update($request->only([
                'number_of_dependents', 'spouse_name', 'spouse_employment_details'
            ]));

            $customer->employment()->update($request->only([
                'employment_status_id', 'income_source_id', 'current_employer',
                'employment_duration', 'job_title_id', 'gross_income', 'net_income',
                'existing_loans_id'
            ]));

            $customer->bank_account()->update($request->only([
                'bank_name', 'account_name', 'account_number', 'iban', 'swift_code'
            ]));

            $customer->education()->update($request->only([
                'education_id', 'university_name'
            ]));

            if(isset($request->relationship_id )) {
                // Step 4: Update Guarantors
                $customer->references()->delete(); // Remove old guarantors
                foreach ($request->relationship_id as $key => $relationshipId) {
                    $userGuarantor = [
                        'user_id' => $customer->id,
                        'relationship_id' => $relationshipId,
                        'guarantor_contact_name' => $request->guarantor_contact_name[$key],
                        'guarantor_contact_number' => $request->guarantor_contact_number[$key],
                    ];

                    UserGuarantor::create($userGuarantor);
                }
            }

             LogActivity::addToLog('Customer  '.$request->name.' Updated');


            // Finalize and Commit
            DB::commit();

            return redirect()->route('show-customer')->with('success', 'Customer Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('show-customer')->with('error', $e->getMessage());
        }
    }




}
