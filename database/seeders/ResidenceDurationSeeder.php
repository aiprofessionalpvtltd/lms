<?php

namespace Database\Seeders;

use App\Models\ResidenceDuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResidenceDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $durations = [
            ['name' => 'More than 3 years'],
            ['name' => '1-3 years'],
            ['name' => 'Less than 1 year'],

        ];

        foreach ($durations as $duration) {
            ResidenceDuration::firstOrCreate($duration);
        }
    }
}
