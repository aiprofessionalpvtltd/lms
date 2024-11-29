<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'user_id',
        'document_type_id',
        'path'
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
