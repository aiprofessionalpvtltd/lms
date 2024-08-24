<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationHistoryResource extends JsonResource
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
            'status' => $this->status,
            'remarks' => $this->remarks,
            'fromUser' => new UserResource($this->fromUser),
            'fromRole' => new RoleResource($this->fromRole),
            'toUser' => new UserResource($this->toUser),
            'toRole' => new RoleResource($this->toRole),
             'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
