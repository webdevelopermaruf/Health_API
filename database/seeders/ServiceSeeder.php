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
            // Outdoor Services
            [
                'name' => 'Emergency Checkup',
                'description' => 'Immediate outpatient checkup for emergency cases.',
                'type' => 2,
                'amount' => 600.00,
                'status' => 1,
            ],
            [
                'name' => 'Emergency Surgery',
                'description' => 'Minor emergency surgical procedures in OPD.',
                'type' => 2,
                'amount' => 2000.00,
                'status' => 1,
            ],
            [
                'name' => 'General Consultation',
                'description' => 'Outdoor consultation with general physicians.',
                'type' => 2,
                'amount' => 500.00,
                'status' => 1,
            ],
            [
                'name' => 'Diabetes Checkup',
                'description' => 'Outpatient diabetes screening and monitoring.',
                'type' => 2,
                'amount' => 400.00,
                'status' => 1,
            ],
            [
                'name' => 'Vaccination',
                'description' => 'Child and adult immunization services.',
                'type' => 2,
                'amount' => 200.00,
                'status' => 1,
            ],
            [
                'name' => 'Minor Dressing',
                'description' => 'Wound care and dressing in OPD.',
                'type' => 2,
                'amount' => 300.00,
                'status' => 1,
            ],
            [
                'name' => 'Physiotherapy',
                'description' => 'Outdoor physiotherapy and rehabilitation sessions.',
                'type' => 2,
                'amount' => 800.00,
                'status' => 1,
            ],

            // Diagnostic Services
            [
                'name' => 'Blood Test (CBC)',
                'description' => 'Complete blood count test.',
                'type' => 3,
                'amount' => 500.00,
                'status' => 1,
            ],
            [
                'name' => 'Blood Sugar Test',
                'description' => 'Random or fasting blood glucose level test.',
                'type' => 3,
                'amount' => 200.00,
                'status' => 1,
            ],
            [
                'name' => 'Urine Test',
                'description' => 'Routine and microscopic examination of urine.',
                'type' => 3,
                'amount' => 150.00,
                'status' => 1,
            ],

            // Indoor
            [
                'name' => 'Indoor Admission Fee',
                'description' => 'Admission to general ward for inpatient care.',
                'type' => 1,
                'amount' => 200.00,
                'status' => 1,
            ],
            [
                'name' => 'Indoor Doctor Round',
                'description' => 'Charge for a doctor visit or round to check the patient in the ward/cabin/ICU.',
                'type' => 1,
                'amount' => 500.00,
                'status' => 1,
            ],
            [
                'name' => 'Bed Transfer Fee',
                'description' => 'Fee applied when a patient is moved to a different bed, cabin, or ward.',
                'type' => 1,
                'amount' => 100.00,
                'status' => 1,
            ],
            [
                'name' => 'Equipment Fee',
                'description' => 'Charge for use of medical equipment such as Oxygen, Nebulizer, Ventilator, and Suction during patient care.',
                'type' => 1,
                'amount' => 2000.00,
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
