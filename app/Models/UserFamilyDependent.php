<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFamilyDependent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number_of_dependents',
        'spouse_name',
        'spouse_employment_details'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
