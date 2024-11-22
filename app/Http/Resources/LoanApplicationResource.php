<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
// Check if attachments exist
        $hasAttachments = $this->attachments && $this->attachments->isNotEmpty();

        return [
            'id' => $this->id,
            'application_id' => $this->application_id,
            'name' => $this->name,
            'email' => $this->email,
            'is_completed' => ($this->is_completed == 1) ? true : false,
            'is_application_submitted' => true,
            'is_documents_uploaded' => $hasAttachments ? true : false,  // Check if there are any attachments
            'is_process_completed' => ($this->is_submitted == 1) ? true : false,
            'loan_amount' => $this->loan_amount,
            'address' => $this->address,
            'reference_contact_2' => $this->reference_contact_2,
            'reference_contact_1' => $this->reference_contact_1,
            'status' => $this->status,
            'product' => new ProductResource($this->product),
            'loan_duration' => new LoanDurationResource($this->loanDuration),
            'product_service' => new ProductServiceResource($this->productService),
            'loan_purpose' => new LoanPurposeResource($this->loanPurpose),
            'user' => new UserResource($this->user),
            'documents' => ($this->attachments != null) ? LoanAttachmentResource::collection($this->attachments) : null, // Conditionally return attachments
            'history' => LoanApplicationHistoryResource::collection($this->histories),
        ];
    }
}
