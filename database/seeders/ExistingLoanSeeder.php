<?php

namespace Database\Seeders;

use App\Models\ExistingLoan;
use Illuminate\Database\Seeder;

class ExistingLoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'No Loan'],
            ['name' => 'Moderate debt (<20% of income)'],
            ['name' => 'High debt (>20% of income)'],

        ];

        foreach ($types as $type) {
            ExistingLoan::firstOrCreate($type);
        }
    }
}
