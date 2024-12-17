<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id', 'issue_date',  'due_date', 'amount_due', 'amount_paid', 'is_paid', 'paid_at' ,'status'
    ];

    // Append the installment_number attribute
    protected $appends = ['installment_number'];

    // Add the installment_number accessor
    public function getInstallmentNumberAttribute()
    {
        // Find the parent Installment and get the index of this detail
        $parent = $this->installment;
        if ($parent) {
            $sortedDetails = $parent->details->sortBy('due_date')->values();
            $index = $sortedDetails->search(function ($item) {
                return $item->id === $this->id;
            });

            return $index !== false ? formatOrdinal($index + 1) : null;
        }

        return null;
    }


    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function recovery()
    {
        return $this->hasOne(Recovery::class, 'installment_detail_id');
    }


}
