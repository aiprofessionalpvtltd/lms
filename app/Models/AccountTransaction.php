<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'account_id',
        'debit',
        'credit',
        'description',
        'reference',
        'transaction_type',
        'related_transaction_id',
        'vendor_account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function relatedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }
    public function vendorAccount()
    {
        return $this->belongsTo(AccountVendor::class);
    }
}
