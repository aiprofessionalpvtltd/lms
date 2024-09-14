<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = [
            ['name' => 'Pakistani', 'emoji' => 'flag-pk'],
            ['name' => 'United States', 'emoji' => 'flag-us'],
            ['name' => 'United Kingdom', 'emoji' => 'flag-gb'],
            ['name' => 'Japan', 'emoji' => 'flag-jp'],
        ];

        foreach ($nationalities as $nationality) {
            Nationality::firstOrCreate([
                'name' => $nationality['name'],
                'emoji' => $nationality['emoji']
            ]);
        }
    }
}
