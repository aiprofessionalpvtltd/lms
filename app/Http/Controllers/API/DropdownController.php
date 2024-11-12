<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentTypeResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\EmploymentStatusResource;
use App\Http\Resources\ExistingLoanResource;
use App\Http\Resources\GenderResource;
use App\Http\Resources\IncomeSourceResource;
use App\Http\Resources\JobTitleResource;
use App\Http\Resources\LoanDurationResource;
use App\Http\Resources\LoanPurposeResource;
use App\Http\Resources\MaritalStatusResource;
use App\Http\Resources\NationalityResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductServiceResource;
use App\Http\Resources\RelationshipResource;
use App\Http\Resources\ResidenceDurationResource;
use App\Http\Resources\ResidenceTypeResource;
use App\Models\City;
use App\Models\District;
use App\Models\DocumentType;
use App\Models\Education;
use App\Models\EmploymentStatus;
use App\Models\ExistingLoan;
use App\Models\Gender;
use App\Models\IncomeSource;
use App\Models\JobTitle;
use App\Models\LoanDuration;
use App\Models\LoanPurpose;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\Product;
use App\Models\ProductService;
use App\Models\Province;
use App\Models\Relationship;
use App\Models\ResidenceDuration;
use App\Models\ResidenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Exception;

class DropdownController extends BaseController
{

    public function getLoanDuration(Request $request)
    {
        try {

            $loanDurations = LoanDuration::all();

            // Check if any loan applications are found
            if ($loanDurations->isEmpty()) {
                return $this->sendError('No Loan Duration found.');
            }

            if ($request->is('api/*')) {
                // Return the loan applications as a response
                return $this->sendResponse(
                    LoanDurationResource::collection($loanDurations),
                    'Loan Duration retrieved successfully.'
                );
            } else {
                //return view('admin.loan_applications.index', compact('loanDurations'));
            }

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Duration Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred while retrieving loan Duration. Please try again later.' . $e->getMessage());
        }
    }

    public function getLoanPurpose(Request $request)
    {
        try {

            $loanPurposes = LoanPurpose::all();

            // Check if any loan applications are found
            if ($loanPurposes->isEmpty()) {
                return $this->sendError('No Loan Purpose found.');
            }

            if ($request->is('api/*')) {
                // Return the loan applications as a response
                return $this->sendResponse(
                    LoanPurposeResource::collection($loanPurposes),
                    'Loan Purpose retrieved successfully.'
                );
            } else {
                //return view('admin.loan_applications.index', compact('loanDurations'));
            }

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Loan Purpose Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred while retrieving loan Purpose. Please try again later.' . $e->getMessage());
        }
    }

    public function getProductService(Request $request)
    {
        try {

            $productServices = ProductService::all();

            // Check if any loan applications are found
            if ($productServices->isEmpty()) {
                return $this->sendError('No Product Service found.');
            }

            if ($request->is('api/*')) {
                // Return the loan applications as a response
                return $this->sendResponse(
                    ProductServiceResource::collection($productServices),
                    'Product Service retrieved successfully.'
                );
            } else {
                //return view('admin.loan_applications.index', compact('loanDurations'));
            }

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Product Service Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred while retrieving Product Service. Please try again later.' . $e->getMessage());
        }
    }

    public function getDocumentType(Request $request)
    {
        try {

            $documentTypes = DocumentType::all();

            // Check if any loan applications are found
            if ($documentTypes->isEmpty()) {
                return $this->sendError('No  document types found.');
            }

            if ($request->is('api/*')) {
                // Return the loan applications as a response
                return $this->sendResponse(
                    DocumentTypeResource::collection($documentTypes),
                    ' document types retrieved successfully.'
                );
            } else {
                //return view('admin.loan_applications.index', compact('loanDurations'));
            }

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error(' document types Retrieval Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred while retrieving document types. Please try again later.' . $e->getMessage());
        }
    }

    public function getGenders()
    {
        try {
            $genders = Gender::all();
            return $this->sendResponse(GenderResource::collection($genders), 'Genders retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving genders.', $e->getMessage());
        }
    }

    public function getMaritalStatuses()
    {
        try {
            $maritalStatuses = MaritalStatus::all();
            return $this->sendResponse(MaritalStatusResource::collection($maritalStatuses), 'Marital statuses retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving marital statuses.', $e->getMessage());
        }
    }

    public function getNationalities()
    {
        try {
            $nationalities = Nationality::all();
            return $this->sendResponse(NationalityResource::collection($nationalities), 'Nationalities retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving nationalities.', $e->getMessage());
        }
    }

    public function getIncomeSource()
    {
        try {
            // Retrieve all income sources
            $incomeSources = IncomeSource::all();

            // Return a successful response with the collection of IncomeSourceResource
            return $this->sendResponse(IncomeSourceResource::collection($incomeSources), 'Income sources retrieved successfully.');
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return $this->sendError('Error retrieving income sources.', $e->getMessage());
        }
    }

    public function getEmploymentStatus()
    {
        try {
            // Retrieve all employment statuses
            $employmentStatuses = EmploymentStatus::all();

            // Return a successful response with the collection of EmploymentStatusResource
            return $this->sendResponse(EmploymentStatusResource::collection($employmentStatuses), 'Employment statuses retrieved successfully.');
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return $this->sendError('Error retrieving employment statuses.', $e->getMessage());
        }
    }

    public function getEducation()
    {
        try {

            $educations = Education::all();

            return $this->sendResponse(EducationResource::collection($educations), 'Education  retrieved successfully.');

        } catch (\Exception $e) {
            // Handle any exceptions
            return $this->sendError('Error retrieving education levels.', $e->getMessage());


        }
    }

    public function getRelationShip()
    {
        try {

            $relationships = Relationship::all();

            return $this->sendResponse(RelationshipResource::collection($relationships), 'Relationship  retrieved successfully.');

        } catch (\Exception $e) {
            // Handle any exceptions
            return $this->sendError('Error retrieving Relationship .', $e->getMessage());


        }
    }


    public function getJobTitle()
    {
        try {

            $titles = JobTitle::all();

            return $this->sendResponse(JobTitleResource::collection($titles), 'Job Title  retrieved successfully.');

        } catch (\Exception $e) {
            // Handle any exceptions
            return $this->sendError('Error retrieving Job Title .', $e->getMessage());


        }
    }


    public function getResidenceTypes()
    {
        try {

            $types = ResidenceType::all();

            return $this->sendResponse(ResidenceTypeResource::collection($types), 'Residence Types  retrieved successfully.');

        } catch (\Exception $e) {
            // Handle any exceptions
            return $this->sendError('Error retrieving Residence Types .', $e->getMessage());


        }
    }

    public function getResidenceDuration()
    {
        try {

            $durations = ResidenceDuration::all();

            return $this->sendResponse(ResidenceDurationResource::collection($durations), 'Residence Durations  retrieved successfully.');

        } catch (\Exception $e) {
            // Handle any exceptions
            return $this->sendError('Error retrieving Residence Duration .', $e->getMessage());


        }
    }

    public function getExistingLoans()
    {
        try {

            $loans = ExistingLoan::all();

            return $this->sendResponse(ExistingLoanResource::collection($loans), ' Loans  retrieved successfully.');

        } catch (\Exception $e) {
            // Handle any exceptions
            return $this->sendError('Error retrieving Loan .', $e->getMessage());


        }
    }

    public function getProvinceByCountry(Request $request)
    {
        try {
            $provinces = Province::select('id', 'name')
                ->where('country_id', $request->country_id)
                ->orderBy('name', 'ASC')
                ->get();

            return $this->sendResponse($provinces, 'Provinces retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving Provinces.', $e->getMessage());
        }
    }

    public function getDistrictByProvince(Request $request)
    {
        try {
            $districts = District::select('id', 'name')
                ->where('province_id', $request->province_id)
                ->orderBy('name', 'ASC')
                ->get();

            return $this->sendResponse($districts, 'Districts retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving Districts.', $e->getMessage());
        }
    }

    public function getCityByProvince(Request $request)
    {
        try {
            $cities = City::select('id', 'name')
                ->where('province_id', $request->province_id)
                ->orderBy('name', 'ASC')
                ->get();

            return $this->sendResponse($cities, 'Cities retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving Cities.', $e->getMessage());
        }
    }


    public function getProducts()
    {
        $authUser = auth()->user();
//        $provinceID = $authUser->province_id;
//        $districtID = $authUser->district_id;

        try {
//            $products = Product::where('province_id', $provinceID)
//                ->where('district_id', $districtID)
//                ->orderBy('name', 'ASC')
//                ->get();
            $products = Product::orderBy('name', 'ASC')
                ->get();

            return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving Products.', $e->getMessage());
        }
    }

}
