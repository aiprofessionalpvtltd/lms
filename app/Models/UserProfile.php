<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender_id',
        'marital_status_id',
        'nationality_id',
        'residence_type_id',
        'residence_duration_id',
        'first_name',
        'last_name',
        'father_name',
        'photo',
        'cnic_front',
        'cnic_back',
        'cnic_no',
        'issue_date',
        'expire_date',
        'dob',
        'mobile_no',
        'alternate_mobile_no',
        'permanent_address',
        'current_address',
        'current_address_duration'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function residenceType()
    {
        return $this->belongsTo(ResidenceType::class);
    }

    public function residenceDuration()
    {
        return $this->belongsTo(ResidenceDuration::class);
    }
}
