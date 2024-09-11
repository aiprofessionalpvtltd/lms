<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplicationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'status',
        'remarks',
        'from_user_id',
        'from_role_id',
        'to_user_id',
        'to_role_id',
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id')->withDefault();
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id')->withDefault();
    }

    public function fromRole()
    {
        return $this->belongsTo(Role::class, 'from_role_id')->withDefault();
    }

    public function toRole()
    {
        return $this->belongsTo(Role::class, 'to_role_id')->withDefault();
    }
}
