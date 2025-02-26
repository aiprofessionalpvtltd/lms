<?php

namespace App\Models;

use App\Http\Resources\UserGuarantorResource;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'name',
        'email_verified_at',
        'password',
        'email',
        'image',
        'province_id',
        'district_id',
        'city_id',
        'is_nacta_clear',
        'is_zindagi_verified',
        'is_account_opened',
        'account_opening_date',
        'zindagi_trace_no',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
//        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',

    ];

    public function isSuperAdmin()
    {
        return $this->roles[0]->name === 'Super Admin';
    }

    public function isAdmin()
    {
        return $this->roles[0]->name === 'Admin';
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function province()
    {
        return $this->belongsTo(Province::class)->withDefault();
    }

    public function district()
    {
        return $this->belongsTo(District::class)->withDefault();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    public function bank_account()
    {
        return $this->hasOne(UserBankAccount::class)->withDefault();
    }

    public function tracking()
    {
        return $this->hasOne(UserProfileTracking::class)->withDefault();
    }

    public function employment()
    {
        return $this->hasOne(UserEmployment::class)->withDefault();
    }

    public function familyDependent()
    {
        return $this->hasOne(UserFamilyDependent::class)->withDefault();
    }

    public function education()
    {
        return $this->hasOne(UserEducation::class)->withDefault();
    }


    public function references()
    {
        return $this->hasMany(UserGuarantor::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }


}
