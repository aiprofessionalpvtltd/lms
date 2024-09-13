<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGuarantorResource extends JsonResource
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
            'user_id' => new UserResource($this->whenLoaded('user')),
            'guarantor_contact_name' => $this->guarantor_contact_name,
            'relationship' => new RelationshipResource($this->whenLoaded('relationship')),
            'guarantor_contact_number' => $this->guarantor_contact_number,
        ];
    }
}
