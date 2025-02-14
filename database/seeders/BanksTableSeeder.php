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
            ['name' => 'ADVANS Microfinance Bank', 'code' => '33'],
            ['name' => 'Al Baraka Bank', 'code' => '21'],
            ['name' => 'Bank Alfalah Limited', 'code' => '25'],
            ['name' => 'Allied Bank Limited (ABL)', 'code' => '2'],
            ['name' => 'Apna Microfinance Bank', 'code' => '24'],
            ['name' => 'Askari Bank Limited', 'code' => '3'],
            ['name' => 'Bank Al Habib', 'code' => '5'],
            ['name' => 'Bank Islami', 'code' => '8'],
            ['name' => 'Bank Makramah Limited', 'code' => '60'],
            ['name' => 'Bank of Khyber (BOK)', 'code' => '41'],
            ['name' => 'Bank of Punjab (BOP)', 'code' => '17'],
            ['name' => 'Bank of Tokyo Mitsubishi', 'code' => '35'],
            ['name' => 'Burj Bank Limited', 'code' => '16'],
            ['name' => 'Citi Bank Limited', 'code' => '17'],
            ['name' => 'Deutsche Bank', 'code' => '36'],
            ['name' => 'Dubai Islamic Bank Pakistan', 'code' => '20'],
            ['name' => 'Faysal Bank Limited', 'code' => '6'],
            ['name' => 'FINCA Microfinance Bank', 'code' => '27'],
            ['name' => 'First Microfinance Bank', 'code' => '37'],
            ['name' => 'First Women Bank Limited', 'code' => '29'],
            ['name' => 'Habib Bank Limited (HBL)', 'code' => '7'],
            ['name' => 'Habib Metro Bank', 'code' => '26'],
            ['name' => 'HBL Microfinance Bank', 'code' => '47'],
            ['name' => 'Industrial and Commercial Bank of China (ICBC)', 'code' => '34'],
            ['name' => 'Industrial Development Bank', 'code' => '38'],
            ['name' => 'JS Bank Limited', 'code' => '1'],
            ['name' => 'KASB Bank Limited', 'code' => '9'],
            ['name' => 'Khushali Bank Limited', 'code' => '45'],
            ['name' => 'MCB Bank Limited', 'code' => '32'],
            ['name' => 'MCB Islamic Bank', 'code' => '46'],
            ['name' => 'Meezan Bank Limited', 'code' => '22'],
            ['name' => 'Mobilink Microfinance Bank (JazzCash)', 'code' => '58'],
            ['name' => 'National Bank of Pakistan (NBP)', 'code' => '28'],
            ['name' => 'NIB Bank Limited', 'code' => '10'],
            ['name' => 'NRSP Microfinance Bank', 'code' => '42'],
            ['name' => 'Oman International Bank', 'code' => '39'],
            ['name' => 'Sada Pay', 'code' => '55'],
            ['name' => 'Samba Bank Limited', 'code' => '11'],
            ['name' => 'Silk Bank', 'code' => '19'],
            ['name' => 'Sindh Bank', 'code' => '23'],
            ['name' => 'SME Bank', 'code' => '40'],
            ['name' => 'Soneri Bank Limited', 'code' => '12'],
            ['name' => 'Standard Chartered Bank Pakistan', 'code' => '13'],
            ['name' => 'State Bank of Pakistan', 'code' => '43'],
            ['name' => 'Telenor Microfinance Bank (Easypaisa)', 'code' => '58'],
            ['name' => 'U Microfinance Bank', 'code' => '30'],
            ['name' => 'United Bank Limited (UBL)', 'code' => '15'],
            ['name' => 'Waseela Microfinance Bank', 'code' => '31'],
            ['name' => 'Zarai Taraqiati Bank Limited (ZTBL)', 'code' => '44'],
            ['name' => 'COC (cash over counter)', 'code' => '000']
        ];

        foreach ($banks as $bank) {
            $existingBank = Bank::where('name', $bank['name'])->first();

            if ($existingBank) {
                if ($existingBank->code !== $bank['code']) {
                    $existingBank->update(['code' => $bank['code']]);
                }
            } else {
                Bank::create($bank);
            }
        }
    }
}

