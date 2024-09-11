<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileTrackingResource extends JsonResource
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
            'user_id' => $this->user_id,
            'is_registration' => $this->is_registration,
            'is_kyc' => $this->is_kyc,
            'is_profile' => $this->is_profile,
            'is_reference' => $this->is_reference,
            'is_utility' => $this->is_utility,
            'is_bank_statement' => $this->is_bank_statement,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
