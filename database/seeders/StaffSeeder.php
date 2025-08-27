<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate the next code for staff (assuming no existing staff)
        $last_code = DB::table('users')->where('code', 'like', 'ST-%')->max('code');
        $next_number = $last_code ? (int) str_replace('ST-', '', $last_code) + 1 : 1000;

        // Define two staff members with corresponding users
        $staffs = [
            [
                'user' => [
                    'code' => 'EMP-' . $next_number,
                    'name' => 'Mr Accountant',
                    'email' => 'sarah.davis@example.com',
                    'phone' => '202-555-0789',
                    'password' => Hash::make('accounts'),
                    'designation' => 'Registered Nurse',
                    'department_id' => 6, // Emergency Medicine (from DepartmentSeeder)
                    'address' => '789 Care Street, City, Country',
                    'dob' => '1990-11-05',
                    'blood' => 'B+',
                    'picture' => "/users/default.png",
                    'gender' => 2, // Female
                    'status' => 1,
                    'permission' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'staff' => [
                    'department_id' => 6, // Emergency Medicine
                    'salary_structure_id' => 1, // Nurse Salary
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            [
                'user' => [
                    'code' => 'EMP-' . ($next_number + 1),
                    'name' => 'Mr Receptionist',
                    'email' => 'michael.brown@example.com',
                    'phone' => '202-555-0987',
                    'password' => Hash::make('reception'),
                    'designation' => 'Receptionist',
                    'department_id' => 6, // Emergency Medicine
                    'address' => '101 Welcome Road, City, Country',
                    'dob' => '1985-07-12',
                    'blood' => 'B+',
                    'picture' => "/users/default.png",
                    'gender' => 0, // Male
                    'status' => 1,
                    'permission' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'staff' => [
                    'department_id' => 6, // Emergency Medicine
                    'salary_structure_id' => 2, // Receptionist Salary
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            [
                'user' => [
                    'code' => 'EMP-' . ($next_number + 2),
                    'name' => 'Mr Lab',
                    'email' => 'lab.brown@example.com',
                    'phone' => '202-555-0687',
                    'password' => Hash::make('lab'),
                    'designation' => 'Laboratorist',
                    'department_id' => 6, // Emergency Medicine
                    'address' => '101 Welcome Road, City, Country',
                    'dob' => '1985-07-12',
                    'blood' => 'O-',
                    'picture' => "/users/default.png",
                    'gender' => 1, // Male
                    'status' => 1,
                    'permission' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'staff' => [
                    'department_id' => 6, // Emergency Medicine
                    'salary_structure_id' => 2, // Salary
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
        ];

        // Insert users and staff
        foreach ($staffs as $record) {
            // Insert user and get ID
            $userId = DB::table('users')->insertGetId($record['user']);
            // Insert staff with user_id, department_id, and salary_structure_id
            DB::table('staffs')->insert(array_merge($record['staff'], [
                'user_id' => $userId,
            ]));
        }
    }
}
