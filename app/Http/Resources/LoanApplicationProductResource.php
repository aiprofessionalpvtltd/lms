<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationProductResource extends JsonResource
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
            'request_for' => $this->request_for,
            'loan_application_id' => $this->loan_application_id,
            'product_id' => $this->product_id,
            'loan_duration_id' => $this->loan_duration_id,
            'loan_amount' => $this->loan_amount,
            'down_payment_percentage' => $this->down_payment_percentage,
            'processing_fee_percentage' => $this->processing_fee_percentage,
            'interest_rate_percentage' => $this->interest_rate_percentage,
            'financed_amount' => $this->financed_amount,
            'processing_fee_amount' => $this->processing_fee_amount,
            'down_payment_amount' => $this->down_payment_amount,
            'total_upfront_payment' => $this->total_upfront_payment,
            'disbursement_amount' => $this->disbursement_amount,
            'total_interest_amount' => $this->total_interest_amount,
            'total_repayable_amount' => $this->total_repayable_amount,
            'monthly_installment_amount' => $this->monthly_installment_amount,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
