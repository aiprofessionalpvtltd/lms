<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guarantor extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'loan_application_guarantors';
    protected $fillable = [
        'loan_application_id',
        'first_name',
        'last_name',
        'cnic_no',
        'address',
        'mobile_no',
        'cnic_attachment',
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }
}
