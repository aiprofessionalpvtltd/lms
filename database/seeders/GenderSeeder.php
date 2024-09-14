<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders =
            [
                ['name' => 'Male', 'emoji' => 'man'],
                ['name' => 'Female', 'emoji' => 'women'],
                ['name' => 'Other', 'emoji' => 'women'],

            ];

        foreach ($genders as $gender) {
            Gender::firstOrCreate([
                'name' => $gender['name'],
                'emoji' => $gender['emoji']
            ]);
        }
    }
}
