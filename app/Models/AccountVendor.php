<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'province_id',
        'district_id',
        'city_id',
        'address',
        'business_name',
        'bank_name',
        'iban_no',
        'cnic_no'
    ];
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function vendorAccount()
    {
        return $this->hasOne(AccountTransaction::class, 'vendor_account_id');
    }
}
