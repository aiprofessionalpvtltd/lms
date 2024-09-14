<?php

namespace Database\Seeders;

use App\Models\LoanDuration;
use Illuminate\Database\Seeder;

class LoanDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $durations = [
            ['name' => '3 Months', 'value' => 3],
            ['name' => '6 Months', 'value' => 6],
            ['name' => '9 Months', 'value' => 9],
            ['name' => '12 Months', 'value' => 12],
        ];

        foreach ($durations as $duration) {
            LoanDuration::firstOrCreate([
                'name' => $duration['name'],
                'value' => $duration['value']
            ]);
        }
    }
}
