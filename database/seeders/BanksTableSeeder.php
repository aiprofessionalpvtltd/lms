<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            'National Bank of Pakistan (NBP)',
            'Bank Alfalah',
            'Bank Islami Pakistan Limited',
            'Bank of Punjab (BOP)',
            'Bank of Khyber (BOK)',
            'Faysal Bank',
            'Habib Bank Limited (HBL)',
            'MCB Bank Limited',
            'Standard Chartered Bank (Pakistan)',
            'United Bank Limited (UBL)',
            'Al Baraka Bank',
            'Summit Bank',
            'First Women Bank Limited',
            'Dubai Islamic Bank Pakistan',
            'U Microfinance Bank',
            'FINCA Microfinance Bank',
            'Mobilink Microfinance Bank/Jazzcash',
            'Bank of Azad Jammu and Kashmir (BAJK)',
            'Bank Al Habib',
            'JS Bank',
            'Bank Islami Pakistan',
            'Silk Bank',
            'Telenor/Easypaisa'
        ];

        foreach ($banks as $bank) {
            if (!Bank::where('name', $bank)->exists()) {
                Bank::create(['name' => $bank]);
            }
        }
    }
}
