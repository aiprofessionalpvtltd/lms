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
            'data'    => $result,
            'message' => $message,
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

        if(!empty($errorMessages)){
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
}
