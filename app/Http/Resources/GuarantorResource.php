<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuarantorResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'cnic_no' => $this->cnic_no,
            'address' => $this->address,
            'mobile_no' => $this->mobile_no,
            'cnic_attachment' => asset('storage/' . $this->cnic_attachment),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
