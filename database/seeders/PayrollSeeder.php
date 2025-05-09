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
        $paymentMethods = ['Credit Card', 'Roket', 'PayPal', 'Bank Transfer', 'Cash', 'Bkash', 'Nogod', 'Upay', "Bank Check"];
        for ($i = 0; $i < count($paymentMethods); $i++) {
            PaymentMethods::insert([
                "name" => $paymentMethods[$i],
                "type" => $paymentMethods[$i] == "Cash"? 1 : 2,
                "details" => "Account Number: ". rand(10000000, 999999999),
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $designations = [
            'Registered Nurse',
            'Receptionist',
            'Lab Assistant',
            'Pharmacist',
            'Accountant',
            'HR Officer',
            'Ward Clerk',
            'Physiotherapist',
            'Technician',
        ];
        for ($i = 0; $i < 5; $i++) {
            $designation = $designations[array_rand($designations)]; //
            SalaryStructure::insert([
                "designation" => $designation,
                "basic_salary" => rand(20, 50) * 1000,
                "salary_type" => rand(1, 5),
                "overtime_rate" => rand(1, 5) * 1000,
                "overtime_type" => rand(1, 5),
                "allowances" => json_encode([]),
                "deductions" => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
