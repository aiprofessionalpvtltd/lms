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
            'loan_amount' => $this->loan_amount,
            'loan_duration' => new LoanDurationResource($this->loanDuration),
            'product_service' => new ProductServiceResource($this->productService),
            'loan_purpose' => new LoanPurposeResource($this->loanPurpose),
            'user' => new UserResource($this->user),
            'address' => $this->address,
            'reference_contact_1' => $this->reference_contact_1,
            'reference_contact_2' => $this->reference_contact_2,
            'status' => $this->status,  // Array of document file pa
            'documents' => $this->documents,  // Array of document file paths
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'history' => LoanApplicationHistoryResource::collection($this->histories),
        ];
    }
}
