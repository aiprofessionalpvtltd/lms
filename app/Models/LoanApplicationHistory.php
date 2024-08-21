<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplicationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'status',
        'remarks',
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }
}
