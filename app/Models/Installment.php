<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{

    use HasFactory;

    protected $fillable = [
        'loan_application_id', 'user_id', 'total_amount', 'monthly_installment',
        'processing_fee', 'total_markup', 'approved_by',
    ];

    public function details()
    {
        return $this->hasMany(InstallmentDetail::class);
    }
}
