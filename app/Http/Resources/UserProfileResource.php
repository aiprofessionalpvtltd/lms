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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'photo' => $this->photo ? Storage::url($this->photo) : null,
            'cnic' => $this->cnic ? Storage::url($this->cnic) : null,
            'cnic_no' => $this->cnic_no,
            'issue_date' => $this->issue_date,
            'expire_date' => $this->expire_date,
            'dob' => $this->dob,
            'mobile_no' => $this->mobile_no,
        ];
    }
}
