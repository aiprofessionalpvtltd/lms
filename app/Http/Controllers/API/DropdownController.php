<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentTypeResource;
use App\Http\Resources\LoanDurationResource;
use App\Http\Resources\LoanPurposeResource;
use App\Http\Resources\ProductServiceResource;
use App\Models\DocumentType;
use App\Models\LoanDuration;
use App\Models\LoanPurpose;
use App\Models\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

}
