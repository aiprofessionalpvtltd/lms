<?php

namespace Database\Seeders;

use App\Models\LoanDuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $durations = [
            '6 Months',
            '1 Year',
            '2 Years'
        ];

        foreach ($durations as $duration) {
            LoanDuration::firstOrCreate(['name' => $duration]);
        }
    }
}
