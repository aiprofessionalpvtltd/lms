<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'loan_amount', 'loan_duration_id', 'user_id',
        'product_service_id', 'loan_purpose_id', 'address',
        'reference_contact_1', 'reference_contact_2', 'documents' ,'status' , 'is_completed' ,'is_submitted'
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function loanDuration()
    {
        return $this->belongsTo(LoanDuration::class);
    }

    public function productService()
    {
        return $this->belongsTo(ProductService::class);
    }

    public function loanPurpose()
    {
        return $this->belongsTo(LoanPurpose::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(LoanApplicationHistory::class);
    }


    public function getLatestHistory()
    {
        return $this->hasOne(LoanApplicationHistory::class)->latestOfMany();
    }

    public function attachments()
    {
        return $this->hasMany(LoanAttachment::class);
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

}
