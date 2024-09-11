<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFamilyDependentResource extends JsonResource
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
            'number_of_dependents' => $this->number_of_dependents,
            'spouse_name' => $this->spouse_name,
            'spouse_employment_details' => $this->spouse_employment_details,
        ];
    }
}
