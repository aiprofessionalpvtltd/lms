<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentResource extends JsonResource
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
            'loan_application_id' => $this->loan_application_id,
            'user_id' => $this->user_id,
            'total_amount' => $this->total_amount,
            'monthly_installment' => $this->monthly_installment,
            'processing_fee' => $this->processing_fee,
            'total_markup' => $this->total_markup,
            'approved_by' => $this->approved_by,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            // Include related installment details as a collection
            'details' => InstallmentDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
