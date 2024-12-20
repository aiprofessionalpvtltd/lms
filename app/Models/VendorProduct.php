<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProduct extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'product_name',
        'description',
        'price',
        'stock_quantity',
    ];

    /**
     * Define the relationship with the Vendor model.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
