<?php

namespace Database\Seeders;

use App\Models\PaymentMethods;
use App\Models\SalaryStructure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = ['Cash','Credit/ Debit Car', 'Roket', 'PayPal', 'Bank Transfer', 'Bkash', 'Nogod', 'Upay', "Bank Cheque"];
        for ($i = 0; $i < count($paymentMethods); $i++) {
            PaymentMethods::insert([
                "name" => $paymentMethods[$i],
                "type" => $paymentMethods[$i] == "Cash" ? 1 : 2,
                "details" => "Account Number: ". rand(10000000, 999999999),
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
        $designations = [
            'Registered Nurse',
            'Accountant',
            'HR & IT Officer',
            'Receptionist',
            'Laboratorist',
            'Lab Assistant',
            'Pharmacist',
            'Physiotherapist',
            'Technician',
            'Ward Clerk',
        ];
        foreach ($designations as $designation) {
            SalaryStructure::insert([
                "designation" => $designation,
                "basic_salary" => rand(20, 50) * 1000,
                "salary_type" => 1, // monthly
                "overtime_rate" => rand(1, 5) * 100,
                "overtime_type" => 4, // hourly
                "allowances" => json_encode([]),
                "deductions" => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
