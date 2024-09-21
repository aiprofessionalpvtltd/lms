<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => new UserProfileResource($this->profile),  // Including the related profile
            'bank_account' => new UserBankAccountResource($this->bank_account),  // Including the related profile
            'employment' => new UserEmploymentResource($this->employment),  // Including the related profile
            'family_dependent' => new UserFamilyDependentResource($this->familyDependent),
            'education' => new UserEducationResource($this->education),
            'references' => UserGuarantorResource::collection($this->references),
            'userTracking' => new UserProfileTrackingResource($this->tracking),  // Including the related profile
        ];
    }
}
