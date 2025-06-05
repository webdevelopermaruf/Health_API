<?php

namespace Database\Seeders;

use App\Models\Services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Indoor Services
            [
                'name' => 'General Ward Admission',
                'description' => 'Admission to general ward for inpatient care.',
                'type' => 1,
                'amount' => 1500.00,
                'status' => 1,
            ],
            [
                'name' => 'ICU Care',
                'description' => 'Intensive care unit services for critical patients.',
                'type' => 1,
                'amount' => 5000.00,
                'status' => 1,
            ],
            [
                'name' => 'Surgical Procedure',
                'description' => 'Inpatient surgical operations.',
                'type' => 1,
                'amount' => 10000.00,
                'status' => 1,
            ],
            [
                'name' => 'Post-Operative Care',
                'description' => 'Care and monitoring after surgery.',
                'type' => 1,
                'amount' => 2000.00,
                'status' => 1,
            ],
            [
                'name' => 'Maternity Ward',
                'description' => 'Delivery and postnatal care services.',
                'type' => 1,
                'amount' => 3000.00,
                'status' => 1,
            ],

            // Outdoor Services
            [
                'name' => 'OPD Consultation',
                'description' => 'Outpatient department consultation with specialists.',
                'type' => 2,
                'amount' => 500.00,
                'status' => 1,
            ],
            [
                'name' => 'Physiotherapy Session',
                'description' => 'Outpatient physiotherapy for rehabilitation.',
                'type' => 2,
                'amount' => 800.00,
                'status' => 1,
            ],
            [
                'name' => 'Wound Dressing',
                'description' => 'Outpatient wound care and dressing.',
                'type' => 2,
                'amount' => 300.00,
                'status' => 1,
            ],
            [
                'name' => 'Vaccination',
                'description' => 'Outpatient immunization services.',
                'type' => 2,
                'amount' => 200.00,
                'status' => 1,
            ],
            [
                'name' => 'Health Checkup',
                'description' => 'Comprehensive outpatient health screening.',
                'type' => 2,
                'amount' => 1000.00,
                'status' => 1,
            ],

            // Diagnostic Services
            [
                'name' => 'Blood Test',
                'description' => 'Complete blood count and other blood diagnostics.',
                'type' => 3,
                'amount' => 400.00,
                'status' => 1,
            ],
            [
                'name' => 'X-Ray',
                'description' => 'Radiographic imaging for diagnosis.',
                'type' => 3,
                'amount' => 600.00,
                'status' => 1,
            ],
            [
                'name' => 'Ultrasound',
                'description' => 'Ultrasound imaging for internal diagnostics.',
                'type' => 3,
                'amount' => 800.00,
                'status' => 1,
            ],
            [
                'name' => 'MRI Scan',
                'description' => 'Magnetic resonance imaging for detailed diagnostics.',
                'type' => 3,
                'amount' => 5000.00,
                'status' => 1,
            ],
            [
                'name' => 'ECG',
                'description' => 'Electrocardiogram for heart diagnostics.',
                'type' => 3,
                'amount' => 300.00,
                'status' => 1,
            ],
        ];

        foreach ($services as $service) {
            Services::insert([
                'name' => $service['name'],
                'description' => ucwords($service['description']),
                'type' => $service['type'],
                'amount' => $service['amount'],
                'status' => $service['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
