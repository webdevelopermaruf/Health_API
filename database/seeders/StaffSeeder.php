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
        $last_code = DB::table('users')->where('code', 'like', 'EMP-%')->max('code');
        $next_number = $last_code ? (int) str_replace('EMP', '', $last_code) + 1 : 1000;

        $designations = [
            ['Registered Nurse', 4, 10],      // Nurse
            ['Accountant', 5, 7],             // Accountant
            ['HR & IT Officer', 6, 6],        // IT Administrator
            ['Receptionist', 4, 9],           // Receptionist
            ['Laboratorist', 7, 12],          // Laboratorist
            ['Lab Assistant', 7, 13],         // Reports Delivery (or create new role if needed)
            ['Pharmacist', 8, 11],            // Pharmacist
            ['Physiotherapist', 9, 14],        // Doctor (if same dashboard), or create a role for Physiotherapist
            ['Technician', 9, 14],             // Doctor (if no specific role), or create role
            ['Ward Clerk', 4, 10],            // Nurse (or create a dedicated Ward Clerk role)
        ];

        foreach ($designations as $index => $item) {
            // Extract designation info
            $designation = $item[0];
            $departmentId = $item[1];
            $roleId = $item[2];

            // Fetch basic_access from roles table
            $permission = DB::table('roles')->where('id', $roleId)->value('basic_access');

            // Prepare user data
            $userData = [
                'code' => 'EMP' . ($next_number + $index),
                'name' => $designation, // can customize name if needed
                'email' => strtolower(str_replace(' ', '.', $designation)) . '@phs.com',
                'phone' => '012345' . rand(10000, 99999),
                'password' => Hash::make('iamstaff'),
                'designation' => $designation,
                'department_id' => $departmentId,
                'address' => 'Subhanighat, Sylhet',
                'dob' => '1990-01-01', // placeholder
                'blood' => 'O+', // placeholder
                'picture' => "/users/default.png",
                'gender' => rand(0,1), // 0: Male, 1: Female
                'status' => 1,
                'roles_id' => $roleId,
                'permission' => $permission, // from roles.basic_access
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert user and get ID
            $userId = DB::table('users')->insertGetId($userData);

            // Prepare staff data
            $staffData = [
                'department_id' => $departmentId,
                'salary_structure_id' => $index + 1, // increment by 1
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $userId,
            ];

            // Insert staff
            DB::table('staffs')->insert($staffData);
        }


    }
}
