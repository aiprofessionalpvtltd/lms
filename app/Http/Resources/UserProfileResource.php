<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'gender' => new GenderResource($this->whenLoaded('gender')),  // Assuming relationship is named 'gender'
            'marital_status' => new MaritalStatusResource($this->whenLoaded('maritalStatus')),  // Assuming relationship is named 'maritalStatus'
            'nationality' => new NationalityResource($this->whenLoaded('nationality')),  // Assuming relationship is named 'nationality'
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'father_name' => $this->father_name,
            'photo' => $this->photo ? Storage::url($this->photo) : null,
            'cnic_front' => $this->cnic_front ? Storage::url($this->cnic_front) : null,
            'cnic_back' => $this->cnic_back ? Storage::url($this->cnic_back) : null,
            'cnic_no' => $this->cnic_no,
            'issue_date' => $this->issue_date,
            'expire_date' => $this->expire_date,
            'dob' => $this->dob,
            'mobile_no' => $this->mobile_no,
            'alternate_mobile_no' => $this->alternate_mobile_no,
            'permanent_address' => $this->permanent_address,
            'current_address' => $this->current_address,
            'current_address_duration' => $this->current_address_duration,
        ];
    }
}
