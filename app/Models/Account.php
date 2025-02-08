<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'account_name_id', 'account_type_id', 'parent_id'];

    public function accountName()
    {
        return $this->belongsTo(AccountName::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
