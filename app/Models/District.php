<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use HasFactory;

    public function province()
    {
        return $this->belongsTo(Province::class)->withDefault();
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

}
