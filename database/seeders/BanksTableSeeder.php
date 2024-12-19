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
            'ADVANS Microfinance Bank',
            'Al Baraka Bank',
            'Bank Alfalah Limited',
            'Allied Bank Limited (ABL)',
            'Apna Microfinance Bank',
            'Askari Bank Limited',
            'Bank Al Habib',
            'Bank Islami',
            'Bank Makramah Limited',
            'Bank of Khyber (BOK)',
            'Bank of Punjab (BOP)',
            'Bank of Tokyo Mitsubishi',
            'Burj Bank Limited',
            'Citi Bank Limited',
            'Deutsche Bank',
            'Dubai Islamic Bank Pakistan',
            'Faysal Bank Limited',
            'FINCA Microfinance Bank',
            'First Microfinance Bank',
            'First Women Bank Limited',
            'Habib Bank Limited (HBL)',
            'Habib Metro Bank',
            'HBL Microfinance Bank',
            'Industrial and Commercial Bank of China (ICBC)',
            'Industrial Development Bank',
            'JS Bank Limited',
            'KASB Bank Limited',
            'Khushali Bank Limited',
            'MCB Bank Limited',
            'MCB Islamic Bank',
            'Meezan Bank Limited',
            'Mobilink Microfinance Bank (JazzCash)',
            'National Bank of Pakistan (NBP)',
            'NIB Bank Limited',
            'NRSP Microfinance Bank',
            'Oman International Bank',
            'Sada Pay',
            'Samba Bank Limited',
            'Silk Bank',
            'Sindh Bank',
            'SME Bank',
            'Soneri Bank Limited',
            'Standard Chartered Bank Pakistan',
            'State Bank of Pakistan',
            'Telenor Microfinance Bank (Easypaisa)',
            'U Microfinance Bank',
            'United Bank Limited (UBL)',
            'Waseela Microfinance Bank',
            'Zarai Taraqiati Bank Limited (ZTBL)',
            'COC (cash over counter)'
        ];

        foreach ($banks as $bank) {
            if (!Bank::where('name', $bank)->exists()) {
                Bank::create(['name' => $bank]);
            }
        }
    }
}
