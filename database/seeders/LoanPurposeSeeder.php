<?php

namespace Database\Seeders;

use App\Models\LoanPurpose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purposes = [
            'Education',
            'Medical',
            'Business'
        ];

        foreach ($purposes as $purpose) {
            LoanPurpose::firstOrCreate(['name' => $purpose]);
        }
    }
}
