<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if all conditions are true
        $isApplicationSubmitted = true;
        $isDocumentsUploaded = ($this->attachments != null) ? true : false;
        $isProcessCompleted = ($this->is_submitted == 1)  ? true : false;

        // Determine if the message should be shown
        $showMessage = $isApplicationSubmitted && $isDocumentsUploaded && $isProcessCompleted;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'loan_amount' => $this->loan_amount,
            'loan_duration' => new LoanDurationResource($this->loanDuration),
            'is_completed' => ($this->is_completed == 1)  ? true : false,
            'is_application_submitted' => $isApplicationSubmitted,
            'is_documents_uploaded' => $isDocumentsUploaded,
            'is_process_completed' => $isProcessCompleted,
            'status' => $this->status,
            'message' => $showMessage ? "An application is already in progress. A new application cannot be submitted." : null
        ];
    }
}
