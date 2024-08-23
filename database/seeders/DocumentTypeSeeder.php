<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            ['name' => 'Passport'],
            ['name' => 'ID Card'],
            ['name' => 'Bank Statement'],
            ['name' => 'Utility Bill'],
            ['name' => '6 Month Salary Slip'],
            ['name' => 'Signature'],
            // Add more types as needed
        ];

        foreach ($documentTypes as $type) {
            DocumentType::firstOrCreate($type);
        }
    }
}
