<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id', 'due_date', 'amount_due', 'amount_paid', 'is_paid',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
