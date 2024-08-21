<?php

namespace Database\Seeders;

use App\Models\ProductService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'Personal Loan',
            'Car Loan',
            'Home Loan'
        ];

        foreach ($services as $service) {
            ProductService::firstOrCreate(['name' => $service]);
        }
    }
}
