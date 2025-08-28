<?php

namespace Database\Seeders;

use App\Models\Auth;
use App\Models\GeneralSettings;
use App\Models\PaymentMethods;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::insert([
            'code' => 'PHS' . 1000, // Generate a unique code
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
            'password' => bcrypt('admin'), // Hash the password
            'department_id' => 2, // Assuming department ID exists (e.g., nurse)
            'address' => '123 Main St, City, Country',
            'dob' => '1990-05-15',
            'blood' => 'O+',
            'gender' => 1,
            'status' => 1,
            'permission' => null, // JSON permissions
        ]);

        Role::insert([
            [
                'id' => 1,
                'name' => 'admin',
            ],
            [
                'id' => 2,
                'name' => 'accountant',
            ],
            [
                'id' => 3,
                'name' => 'receptionist',
            ],
            [
                'id' => 4,
                'name' => 'doctor',
            ],
            [
                'id' => 5,
                'name' => 'nurse',
            ],
            [
                'id' => 6,
                'name' => 'pharmacy',
            ],
            [
                'id' => 7,
                'name' => 'laboratorist',
            ],
            [
                'id' => 8,
                'name' => 'report-delivery',
            ]
        ]);
        $faker = Faker::create();
        GeneralSettings::insert([
            "user_code" => "PHS",
            "name" => json_encode(["en"=> "Prime Hospital", "bn"=> "প্রাইম হসপিটাল"], JSON_UNESCAPED_UNICODE),
            "address" => json_encode(["en"=> "Bishwa Road, Subhanighat, Sylhet-3100", "bn"=> "বিশ্বরোড, সুবহানীঘাট, সিলেট-৩১০০", "hotline"=>"+8801714820333"], JSON_UNESCAPED_UNICODE),
            "icon" => json_encode(["icon"=> "/images/logo.jpg"], JSON_UNESCAPED_UNICODE),
            "sms_api" => json_encode(["balance"=> 0, "rate"=> 0.35, "apikey"=> "8XYpeBwf0JMssLw3Cyxf"]),
            "attendance" => json_encode([]),
            "sms_rules" => json_encode([]),
            "payroll_rules" => json_encode([]),
        ]);

        // other seeder calling
        $this->call([
            FeatureSeeder::class,
            DepartmentSeeder::class,
            DoctorSeeder::class,
            StaffSeeder::class,
            PayrollSeeder::class,
            ServiceSeeder::class,
            PatientSeeder::class,
            ResourceSeeder::class,
            PharmacySeeder::class,
        ]);

    }
}
