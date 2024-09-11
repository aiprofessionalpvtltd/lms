<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = ['Pakistani', 'Other'];

        foreach ($nationalities as $nationality) {
            Nationality::firstOrCreate(['name' => $nationality]);
        }
    }
}
