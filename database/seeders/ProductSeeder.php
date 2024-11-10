<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Honda',
            'price' => '157000',
            'detail' => 'This Bike Financing for Islamabad only',
            'processing_fee' => 2.7,
            'interest_rate' => 30.00,
            'province_id' => 156, // islamabad
            'district_id' => 220, //  islamabad
        ]);
    }
}
