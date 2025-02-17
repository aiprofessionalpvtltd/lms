<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description' ,'is_credit','is_debit'];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
