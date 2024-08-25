<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBankAccount extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_name',
        'account_number',
        'iban',
        'swift_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
