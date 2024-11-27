<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentDetailResource extends JsonResource
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
            'installment_number' => $this->installment_number,
            'installment_id' => $this->installment_id,
            'due_date' => $this->due_date,
            'amount_due' => $this->amount_due,
            'amount_paid' => $this->amount_paid,
            'is_paid' => $this->is_paid,
            'recovery' => $this->recovery ? [
                'amount' => $this->recovery->amount,
                'overdue_days' => $this->recovery->overdue_days,
                'penalty_fee' => $this->recovery->penalty_fee,
                'total_amount' => $this->recovery->total_amount,
                'payment_method' => $this->recovery->payment_method,
                'status' => $this->recovery->status,
                'remarks' => $this->recovery->remarks,
            ] : null,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Customize the response for a null resource.
     */
    public function toResponse($request)
    {
        return $this->resource ? parent::toResponse($request) : response()->json([]);
    }
}
