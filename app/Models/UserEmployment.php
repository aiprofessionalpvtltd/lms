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
        'job_title',
        'gross_income',
        'net_income',
        'existing_loans'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status_id');
    }

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class, 'income_source_id');
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    public function job_title()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }
}
