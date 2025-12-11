<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'description' => 'Cash and cash equivalents'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'description' => 'Amounts owed by customers'],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset', 'description' => 'Stock and inventory'],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'description' => 'Prepaid expenses'],
            ['code' => '1500', 'name' => 'Equipment', 'type' => 'asset', 'description' => 'Office equipment and machinery'],
            
            // Liabilities
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'description' => 'Amounts owed to suppliers'],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'liability', 'description' => 'Accrued liabilities'],
            ['code' => '2200', 'name' => 'Short-term Loans', 'type' => 'liability', 'description' => 'Short-term borrowings'],
            
            // Equity
            ['code' => '3000', 'name' => 'Capital', 'type' => 'equity', 'description' => 'Owner\'s capital'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'description' => 'Accumulated profits'],
            
            // Revenue
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'description' => 'Revenue from sales'],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'description' => 'Revenue from services'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'revenue', 'description' => 'Other income sources'],
            
            // Expenses
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'description' => 'Direct costs of sales'],
            ['code' => '5100', 'name' => 'Salaries Expense', 'type' => 'expense', 'description' => 'Employee salaries'],
            ['code' => '5200', 'name' => 'Rent Expense', 'type' => 'expense', 'description' => 'Office rent'],
            ['code' => '5300', 'name' => 'Utilities Expense', 'type' => 'expense', 'description' => 'Electricity, water, etc.'],
            ['code' => '5400', 'name' => 'Marketing Expense', 'type' => 'expense', 'description' => 'Advertising and marketing'],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
