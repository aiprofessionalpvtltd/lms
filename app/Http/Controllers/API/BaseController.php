<?php

namespace App\Http\Controllers\API;

use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\String\s;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function saveBase64Image(string $base64Image, string $folder): string
    {
        // Split the base64 string into two parts: the metadata and the actual data
        list($metadata, $data) = explode(',', $base64Image);

        // Extract the image type from the metadata (e.g., 'jpeg', 'png')
        preg_match('/data:image\/(\w+);base64/', $metadata, $matches);
        $imageType = $matches[1];

        // Decode the base64 data
        $imageData = base64_decode($data);

        // Generate a unique file name
        $fileName = uniqid() . '.' . $imageType;

        // Define the path where the image will be saved
        $filePath = $folder . '/' . $fileName;

        // Save the image to the specified folder within the storage/app/public directory
        Storage::disk('public')->put($filePath, $imageData);

        // Return the path where the image is saved
        return $filePath;
    }


    public function calculateUserScore($user)
    {
        $score = 0;

        // Personal Information - Age
        $age = Carbon::parse($user->profile->dob)->diffInYears(now());

        if ($age >= 20 && $age <= 40) {
            $score += 5;
        } elseif ($age >= 41 && $age <= 60) {
            $score += 3;
        } elseif ($age > 60) {
            $score += 1;
        }

        // Employment and Financial Information
        if ($employment = optional($user->employment)) {
            switch ($employment->employmentStatus->status) {
                case 'Employed':
                    $score += 5;
                    break;
                case 'Self-employed':
                    $score += 4;
                    break;
                default:
                    $score += 1;
                    break;
            }

            switch ($employment->job_title->name) {
                case 'Senior/Managerial':
                    $score += 5;
                    break;
                case 'Mid-level':
                    $score += 3;
                    break;
                default:
                    $score += 1;
                    break;
            }


            if ($employment->gross_income > 150000) {
                $score += 10;
            } elseif ($employment->gross_income >= 50000) {
                $score += 7;
            } else {
                $score += 3;
            }

            switch ($employment->incomeSource->name) {
                case 'Salary':
                    $score += 5;
                    break;
                case 'Business income':
                    $score += 4;
                    break;
                case 'Rental income':
                    $score += 3;
                    break;
                default:
                    $score += 2;
                    break;
            }


            switch ($employment->existingLoan->id) {
                case '1':
                    $score += 5;
                    break;
                case '2':
                    $score += 3;
                    break;
                case '3':
                    $score += 1;
                    break;
                default:
                    $score += 0;
                    break;
            }


        }

        // Family and Dependents Information
        if ($user->familyDependent->number_of_dependents == 0) {
            $score += 5; // No dependents
        } elseif ($user->familyDependent->number_of_dependents <= 2) {
            $score += 3; // 1-2 dependents
        } else {
            $score += 1; // More than 2 dependents
        }

        $score += optional($user->employment->employmentStatus)->status === 'Employed' ? 5 : 2;

        // Background Information
        $maxEducationScore = 0;

        if ($educations = $user->education) {
            switch ($educations->education->name) {
                case "Bachelor’s Degree":
                case "Post Graduate":
                    $maxEducationScore = max($maxEducationScore, 5);
                    break;
                case 'Secondary Education (High School)':
                    $maxEducationScore = max($maxEducationScore, 3);
                    break;
                default:
                    $maxEducationScore = max($maxEducationScore, 1);
                    break;
            }

            $score += $maxEducationScore;
        }


        // Background Information - Guarantors
        if ($references = optional($user->references)) {
            $guarantorCount = $references->count();
            if ($guarantorCount >= 2) {
                $score += 5;
            } elseif ($guarantorCount == 1) {
                $score += 3;
            } else {
                $score += 0; // No guarantors
            }
        }

        // Marital Status
        if ($profile = optional($user->profile)) {

            switch ($profile->marital_status_id) {
                case '1': // Married
                    $score += 5;
                    break;
                case '2': // Single
                    $score += 3;
                    break;
                default:
                    $score += 1;
                    break;
            }

            // Nationality
            $score += $profile->nationality_id == '1' ? 5 : 2;

            // Residential Information
            switch ($profile->residenceType->name) {
                case 'Own house':
                    $score += 10;
                    break;
                case 'Rented':
                    $score += 5;
                    break;
                default:
                    $score += 1;
                    break;
            }

            switch ($profile->residenceDuration->id) {
                case '1': // More than 3 years
                    $score += 5;
                    break;
                case '2': // 1-3 years
                    $score += 3;
                    break;
                default: // Less than 1 year
                    $score += 1;
                    break;
            }
        }

        // Contact Information
        if ($user->profile->mobile_no || $user->profile->alternate_mobile_no) {
            $score += 5;
        }

        $user->tracking->score = $score;
        $user->tracking->save(); // Save the updated score to the database

        return $user;
    }

    public function determineRiskLevel($score)
    {
        if ($score >= 85 && $score <= 100) {
            return [
                'risk_level' => 'Low Risk',
                'loan_eligibility' => 'Eligible for larger loan amounts at lower interest rates.'
            ];
        } elseif ($score >= 65 && $score <= 84) {
            return [
                'risk_level' => 'Moderate Risk',
                'loan_eligibility' => 'Eligible for moderate loan amounts with standard interest rates.'
            ];
        } elseif ($score < 65 && $score > 0) {
            return [
                'risk_level' => 'High Risk',
                'loan_eligibility' => 'Eligible for small loan amounts with higher interest rates or may require stricter terms (e.g., collateral).'
            ];
        } else {
            return [
                'risk_level' => '',
                'loan_eligibility' => ''
            ];
        }
    }


    function generateLoanApplicationId($user) {
        $authUser = $user;
        $userProvince = $authUser->province->name ?? null;
        $userId = $authUser->id;
        $year = date('y');

        // Define province prefixes
        $provincePrefixes = [
            'Punjab' => 'PJ',
            'Sindh' => 'SN',
            'KPK' => 'KP',
            'Balochistan' => 'BL',
            'Gilgit–Baltistan' => 'GB',
            'AJK' => 'AJK',
            'Federal' => 'ISB',
        ];

        // Get the prefix for the province, default to 'NA'
        $prefix = $provincePrefixes[$userProvince] ?? 'NA';

        // Generate the base application ID
        $baseApplicationId = sprintf('%s-%s-%04d', $prefix, $year, $userId);

        // Find the next unique ID
        $count = 1;
        do {
            $applicationId = sprintf('%s-%03d', $baseApplicationId, $count);
            $existingApplication = LoanApplication::where('application_id', $applicationId)->exists();
            $count++;
        } while ($existingApplication);

        return $applicationId;
    }

    function updateLoanApplicationId($loanApplicationId) {
        // Find the loan application by the given ID
        $loanApplication = LoanApplication::with('user.province')->find($loanApplicationId);

        if (!$loanApplication) {
            return false; // Loan application not found
        }

        // Get user and other details
        $authUser = $loanApplication->user;
        $userProvince = $authUser->province->name ?? null;
        $userId = $authUser->id;
        $year = date('y');

        // Define province prefixes
        $provincePrefixes = [
            'Punjab' => 'PJ',
            'Sindh' => 'SN',
            'KPK' => 'KP',
            'Balochistan' => 'BL',
            'Gilgit–Baltistan' => 'GB',
            'AJK' => 'AJK',
            'Federal' => 'ISB',
        ];

        // Get the prefix for the province, default to 'NA'
        $prefix = $provincePrefixes[$userProvince] ?? 'NA';

        // Generate the base application ID
        $baseApplicationId = sprintf('%s-%s-%04d', $prefix, $year, $userId);

        // Find the next unique ID
        $count = 1;
        do {
            $newApplicationId = sprintf('%s-%03d', $baseApplicationId, $count);
            $existingApplication = LoanApplication::where('application_id', $newApplicationId)
                ->where('id', '!=', $loanApplication->id) // Exclude the current loan application
                ->exists();
            $count++;
        } while ($existingApplication);

        // Update the loan application with the new ID
        if ($loanApplication->application_id !== $newApplicationId) {
            $loanApplication->application_id = $newApplicationId;
            $loanApplication->save();
        }

        return $newApplicationId;
    }





}
