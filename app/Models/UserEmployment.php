<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employment_status_id',
        'income_source_id',
        'current_employer',
        'employment_duration',
        'job_title_id',
        'gross_income',
        'net_income',
        'existing_loans_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status_id')->withDefault();
    }

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class, 'income_source_id')->withDefault();;
    }

    public function job_title()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id')->withDefault();
    }

    public function existingLoan()
    {
        return $this->belongsTo(ExistingLoan::class, 'existing_loans_id')->withDefault();
    }
}
