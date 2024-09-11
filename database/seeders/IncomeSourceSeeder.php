<?php

namespace Database\Seeders;

use App\Models\IncomeSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Salary',
            'Business Income',
            'Rental Income',
            'Investment Income',
            'Other',
        ];

        foreach ($sources as $source) {
            IncomeSource::firstOrCreate(['source' => $source]);
        }
    }
}
