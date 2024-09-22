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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_completed' => $this->is_completed,
            'is_application_submitted' => true,
            'is_documents_uploaded' => ($this->attachments != null) ? true : false,
            'is_process_completed' => $this->is_submitted,
            'loan_amount' => $this->loan_amount,
            'address' => $this->address,
            'reference_contact_2' => $this->reference_contact_2,
            'reference_contact_1' => $this->reference_contact_1,
            'status' => $this->status,
            'loan_duration' => new LoanDurationResource($this->loanDuration),
            'product_service' => new ProductServiceResource($this->productService),
            'loan_purpose' => new LoanPurposeResource($this->loanPurpose),
            'user' => new UserResource($this->user),
            'documents' => ($this->attachments != null) ? LoanAttachmentResource::collection($this->attachments) : null, // Conditionally return attachments
            'history' => LoanApplicationHistoryResource::collection($this->histories),
        ];
    }
}
