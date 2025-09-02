<?php

namespace Database\Seeders;

use App\Models\AppointmentSchedule;
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
        $last_code = DB::table('users')->where('code', 'like', 'DR%')->max('code');
        $next_number = $last_code ? (int) str_replace('DR', '', $last_code) + 1 : 1000;
        $permission = DB::table('roles')->where('id', 8)->value('basic_access');

        // Define two doctors with corresponding users
        $doctors = [
            [
                'user' => [
                    'code' => 'DR' . $next_number,
                    'name' => 'Dr. X',
                    'email' => 'drx@phs.com',
                    'phone' => '012345' . rand(10000, 99999),
                    'password' => Hash::make('iamdoctor'),
                    'designation' => 'Senior Doctor',
                    'department_id' => 5,
                    'address' => 'Sylhet',
                    'dob' => '1975-06-15',
                    'blood' => 'O+',
                    'picture' => "/users/default.png",
                    'gender' => 1,
                    'status' => 1,
                    'permission' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'doctor' => [
                    'specialization' => 'Medicine',
                    'license_number' => 'LIC123456',
                    'qualification' => 'MD, Medicine Fellowship',
                    'experience' => '15 years in medicine',
                    'about' => 'Expert in general disease treatment and prevention.',
                    'availability' => "Every Mon, Wed at 10:00AM - 5:00PM (Lunch Break: 1:00PM - 2:00PM)",
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
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
        $schedules = [
            "sun"   => ["14:00-21:00"], // 2:00 pm - 9:00 pm
            "tue"  => ["14:00-21:00"],
            "thu" => ["14:00-21:00"],
            "fri"   => ["14:00-21:00"]
        ];
        // insert schedules
        AppointmentSchedule::insert([
            "doctors_id" => 1,
            "rooms_id" => 1,
            "fee" => 500,
            "schedule" => json_encode($schedules),
            "appointment_date" =>null,
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
