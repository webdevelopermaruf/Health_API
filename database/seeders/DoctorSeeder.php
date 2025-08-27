<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate the next code (assuming no existing doctors)
        $last_code = DB::table('users')->where('code', 'like', 'DR-%')->max('code');
        $next_number = $last_code ? (int) str_replace('DR-', '', $last_code) + 1 : 1000;

        // Define two doctors with corresponding users
        $doctors = [
            [
                'user' => [
                    'code' => 'DR-' . $next_number,
                    'name' => 'Dr. Medicine',
                    'email' => 'john.smith@example.com',
                    'phone' => '202-555-0123',
                    'password' => Hash::make('password123'),
                    'designation' => 'Senior Cardiologist',
                    'department_id' => 1, // Cardiology (from DepartmentSeeder)
                    'address' => '123 Heart Lane, City, Country',
                    'dob' => '1975-06-15',
                    'blood' => 'O+',
                    'picture' => "/users/default.png",
                    'gender' => 1, // Male
                    'status' => 1,
                    'permission' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'doctor' => [
                    'specialization' => 'Cardiology',
                    'license_number' => 'LIC123456',
                    'qualification' => 'MD, Cardiology Fellowship',
                    'experience' => '15 years in cardiovascular medicine',
                    'about' => 'Expert in heart disease treatment and prevention.',
                    'availability' => json_encode(['Mon-Fri' => '9:00-17:00']),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            [
                'user' => [
                    'code' => 'DR-' . ($next_number + 1),
                    'name' => 'Dr. Surgery',
                    'email' => 'emily.johnson@example.com',
                    'phone' => '202-555-0456',
                    'password' => Hash::make('password123'),
                    'designation' => 'Neurologist',
                    'department_id' => 3, // Neurology (from DepartmentSeeder)
                    'address' => '456 Brain Ave, City, Country',
                    'dob' => '1980-03-22',
                    'blood' => 'A-',
                    'picture' => "/users/default.png",
                    'gender' => 2, // Female
                    'status' => 1,
                    'permission' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'doctor' => [
                    'specialization' => 'Neurology',
                    'license_number' => 'LIC789012',
                    'qualification' => 'MD, PhD in Neuroscience',
                    'experience' => '10 years in neurological disorders',
                    'about' => 'Specializes in epilepsy and stroke management.',
                    'availability' => json_encode(['Mon-Wed' => '10:00-16:00', 'Fri' => '12:00-18:00']),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
        ];

        // Insert users and doctors
        foreach ($doctors as $record) {
            // Insert user and get ID
            $userId = DB::table('users')->insertGetId($record['user']);

            // Insert doctor with user_id and department_id
            DB::table('doctors')->insert(array_merge($record['doctor'], [
                'user_id' => $userId,
                'department_id' => $record['user']['department_id'],
            ]));
        }
    }
}
