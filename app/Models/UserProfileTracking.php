<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileTracking extends Model
{
    use HasFactory;

    use HasFactory;

    // Define the table name, if it's not the plural form of the model name
    protected $table = 'user_profile_tracking';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'is_registration',
        'is_kyc',
        'is_profile',
        'is_reference',
        'is_utility',
        'is_bank_statement',
        'is_address_proof',
        'is_eligibility',
    ];

    // Default attribute values for the model
    protected $attributes = [
        'is_registration' => false,
        'is_kyc' => false,
        'is_profile' => false,
        'is_reference' => false,
        'is_utility' => false,
        'is_bank_statement' => false,
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
