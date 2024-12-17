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
        $durations = [];

        for ($i = 1; $i <= 12; $i++) {
            $durations[] = ['name' => "{$i} Month" . ($i > 1 ? 's' : ''), 'value' => $i];
        }

        foreach ($durations as $duration) {
            LoanDuration::firstOrCreate([
                'name' => $duration['name'],
                'value' => $duration['value']
            ]);
        }

    }
}
