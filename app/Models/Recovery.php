<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recovery extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_detail_id',
        'installment_id',
        'amount',
        'overdue_days',
        'penalty_fee',
        'total_amount',
        'payment_method',
        'status',
        'remarks'
    ];

    public function installmentDetail()
    {
        return $this->belongsTo(InstallmentDetail::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
