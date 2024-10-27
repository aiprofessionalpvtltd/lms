<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Storage;

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
        $age = now()->diffInYears($user->dob);
        if ($age >= 20 && $age <= 40) {
            $score += 5;
        } elseif ($age >= 41 && $age <= 60) {
            $score += 3;
        } elseif ($age > 60) {
            $score += 1;
        }

        // Employment and Financial Information
        switch ($user->employment->employment_status_id) {
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

        switch ($user->employment->job_title_id) {
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

        if ($user->employment->gross_income > 150000) {
            $score += 10;
        } elseif ($user->gross_income >= 50000) {
            $score += 7;
        } else {
            $score += 3;
        }

        switch ($user->employment->income_source_id) {
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

        if ($user->employment->existing_loans == 0) {
            $score += 5;
        } elseif ($user->existing_loans < 0.2 * $user->gross_income) {
            $score += 3;
        } else {
            $score += 1;
        }

        // Family and Dependents Information
        if ($user->number_of_dependents == 0) {
            $score += 5;
        } elseif ($user->number_of_dependents <= 2) {
            $score += 3;
        } else {
            $score += 1;
        }

        $score += $user->familyDependent->spouse_employment_details == 'Employed' ? 5 : 2;

        // Background Information
        switch ($user->education->education_id) {
            case 'Graduate':
            case 'Post-graduate':
                $score += 5;
                break;
            case 'High school diploma':
                $score += 3;
                break;
            default:
                $score += 1;
                break;
        }

        if ($user->references->guarantor_contact_name && $user->references->guarantor_contact_number) {
            $score += $user->relationship_id == 'multiple' ? 5 : 3;
        }

        // Marital Status
        switch ($user->profile->marital_status_id) {
            case '1':
                $score += 5;
                break;
            case '2':
                $score += 3;
                break;
            default:
                $score += 1;
                break;
        }

        // Nationality
        $score += $user->profile->nationality_id == '1' ? 5 : 2;

        // Contact Information
        if ($user->mobile_no || $user->alternate_mobile_no) {
            $score += 5;
        }

        // Residential Information
        switch ($user->profile->residence_type_id) {
            case 'Own':
                $score += 10;
                break;
            case 'Rented':
                $score += 5;
                break;
            default:
                $score += 1;
                break;
        }


        switch ($user->profile->residence_duration_id) {
            case '1':
                $score += 5;
                break;
            case '2':
                $score += 3;
                break;
            default:
                $score += 1;
                break;
        }


        return $score;
    }

}
