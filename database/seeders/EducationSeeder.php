<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationLevels = [
            ['name' => 'No Formal Education'],
            ['name' => 'Primary Education'],
            ['name' => 'Secondary Education (High School)'],
            ['name' => 'Vocational Training/Technical Certification'],
            ['name' => 'Diploma'],
            ['name' => 'Associate Degree'],
            ['name' => 'Bachelor’s Degree'],
            ['name' => 'Master’s Degree'],
            ['name' => 'Doctorate (PhD)'],
        ];

        foreach ($educationLevels as $level) {
            Education::firstOrCreate($level);
        }
    }
}
