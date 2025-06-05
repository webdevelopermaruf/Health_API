<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private function generateBangladeshiPhoneNumber()
    {
        $prefix = '01' . rand(3, 9); // BD numbers: 013–019
        $suffix = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT); // 8-digit number
        return $prefix . $suffix;
    }

    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $i) {
            Patient::insert([
                'name'     => $faker->name,
                'nid'      => $faker->unique()->numerify('##########'),
                'phone'    => $this->generateBangladeshiPhoneNumber(),
                'dob'      => $faker->optional()->date('Y-m-d', '2005-12-31'),
                'blood'    => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                'gender'   => $faker->numberBetween(0, 1),
                'address'  => $faker->optional()->address,
                'status'   => 1,
            ]);
        }
    }
}
