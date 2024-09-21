<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEmploymentResource extends JsonResource
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
            'employment_status' => new EmploymentStatusResource($this->whenLoaded('employmentStatus')),
            'income_source' => new IncomeSourceResource($this->whenLoaded('incomeSource')),
            'current_employer' => $this->current_employer,
            'employment_duration' => $this->employment_duration,
            'job_title' => $this->job_title,
            'gross_income' => $this->gross_income,
            'net_income' => $this->net_income,
            'existing_loans' => $this->existing_loans,
        ];;
    }
}
