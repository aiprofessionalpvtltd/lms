<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'detail' => $this->detail,
            'processing_fee' => $this->processing_fee,
            'interest_rate' => $this->interest_rate,
            'province' => $this->province->name ?? null,
            'district' => $this->district->name ?? null,

        ];
    }
}
