<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountName;
use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $types = [
            ['name' => 'Cash and cash equivalent', 'description' => 'Cash and cash equivalent'],
            ['name' => 'Current Asset', 'description' => 'Loan amounts owed by customers'],
            ['name' => 'Non Current Asset', 'description' => 'Company investment'],
            ['name' => 'Current Liabilities', 'description' => 'Short term obligations'],
            ['name' => 'Non Current Liabilities', 'description' => 'Long term obligations'],
            ['name' => 'Income', 'description' => 'Revenue sources'],
            ['name' => 'Expenses', 'description' => 'Company expenditures'],
            ['name' => 'Fixed Assets', 'description' => 'Long-term tangible property'],
            ['name' => 'Owner’s equity', 'description' => 'Capital investment'],
            ['name' => 'Cost of sale', 'description' => 'Expenses related to sales'],
        ];

        $typeIds = [];
        foreach ($types as $type) {
            $typeIds[$type['name']] = AccountType::firstOrCreate($type)->id;
        }

        $accounts = [
            ['code' => '101', 'name' => 'Cash in Bank', 'type' => 'Cash and cash equivalent'],
            ['code' => '102', 'name' => 'Accounts Receivable', 'type' => 'Current Asset'],
            ['code' => '103', 'name' => 'Long Term Investments', 'type' => 'Non Current Asset'],
            ['code' => '201', 'name' => 'Accounts Payable', 'type' => 'Current Liabilities'],
            ['code' => '202', 'name' => 'Loan Payable', 'type' => 'Current Liabilities'],
            ['code' => '203', 'name' => 'Loan Term Borrowings', 'type' => 'Non Current Liabilities'],
            ['code' => '301', 'name' => 'Revenue from Loans', 'type' => 'Income'],
            ['code' => '302', 'name' => 'Service Charges', 'type' => 'Income'],
            ['code' => '303', 'name' => 'Interest Income', 'type' => 'Income'],
            ['code' => '401', 'name' => 'Operating Expenses', 'type' => 'Expenses'],
            ['code' => '502', 'name' => 'Depreciation Expense', 'type' => 'Expenses'],
            ['code' => '601', 'name' => 'Machinery/Office Equipment/Computer', 'type' => 'Fixed Assets'],
            ['code' => '701', 'name' => 'Owner’s equity', 'type' => 'Owner’s equity'],
            ['code' => '801', 'name' => 'Purchases', 'type' => 'Cost of sale'],
        ];

        foreach ($accounts as $account) {
            $accountName = AccountName::firstOrCreate(['name' => $account['name']]);
            Account::create([
                'code' => $account['code'],
                'account_name_id' => $accountName->id,
                'account_type_id' => $typeIds[$account['type']],
                'parent_id' => null,
            ]);
        }
    }
}
