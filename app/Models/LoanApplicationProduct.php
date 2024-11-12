<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplicationProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loan_application_products';

    protected $fillable = [
        'request_for',
        'loan_application_id',
        'product_id',
        'loan_duration_id',
        'loan_amount',
        'down_payment_percentage',
        'processing_fee_percentage',
        'interest_rate_percentage',
        'financed_amount',
        'processing_fee_amount',
        'down_payment_amount',
        'total_upfront_payment',
        'disbursement_amount',
        'total_interest_amount',
        'total_repayable_amount',
        'monthly_installment_amount',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'down_payment_percentage' => 'decimal:2',
        'processing_fee_percentage' => 'decimal:2',
        'interest_rate_percentage' => 'decimal:2',
        'financed_amount' => 'decimal:2',
        'processing_fee_amount' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
        'total_upfront_payment' => 'decimal:2',
        'disbursement_amount' => 'decimal:2',
        'total_interest_amount' => 'decimal:2',
        'total_repayable_amount' => 'decimal:2',
        'monthly_installment_amount' => 'decimal:2',
    ];

    /**
     * Relationship with Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship with LoanDuration.
     */
    public function loanDuration()
    {
        return $this->belongsTo(LoanDuration::class, 'loan_duration_id');
    }
}
