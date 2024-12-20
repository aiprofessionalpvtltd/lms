<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'price',
        'detail',
        'processing_fee',
        'interest_rate',
        'province_id',
        'district_id',
        'vendor_id',
        'vendor_product_id',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function vendorProduct()
    {
        return $this->belongsTo(VendorProduct::class);
    }

}
