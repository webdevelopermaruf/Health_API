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
            'name' => 'Md Ashraful Islam',
            'email' => 'admin@phs.com',
            'phone' => '1234567890',
            'password' => bcrypt('admin'), // Hash the password
            'department_id' => 1,
            'address' => 'Sylhet',
            'dob' => '1990-01-01',
            'blood' => 'O+',
            'roles_id' => 1, // admin
            'gender' => 1,
            'status' => 1,
            'permission' => null, // JSON permissions full control
        ]);

        Role::insert([
            [
                'id' => 1,
                'name' => 'Administrator', // access discount. & Chairman Account.
                'dashboard' => 'admin',
                'basic_access' => null
            ],
            [
                'id' => 2,
                'name' => 'Managing Director', // access discount
                'dashboard' => 'admin',
                'basic_access' => json_encode([1=>1,6=>1,7=>1,8=>1,9=>1,1001=>0])
            ],
            [
                'id' => 3,
                'name' => 'Executive Director', // can see all option
                'dashboard' => 'director',
                'basic_access' => json_encode([1=>0,2=>1,3=>1,4=>1,5=>1,6=>0,7=>0,8=>0,9=>1,10=>0])
            ],
            [
                'id' => 4,
                'name' => 'Director',
                'dashboard' => 'director',
                'basic_access' => json_encode([1=>0,2=>1,3=>1,4=>1,5=>1,6=>0,7=>0,8=>0,9=>1,10=>0])
            ],
            [
                'id' => 5,
                'name' => 'Shareholder',
                'dashboard' => 'shareholder',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0])
            ],
            [
                'id' => 6,
                'name' => 'IT Administrator',
                'dashboard' => 'it',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0])
            ],
            [
                'id' => 7,
                'name' => 'Accountant',
                'dashboard' => 'accounts',
                'basic_access' => json_encode([1=>0,2=>1,3=>1,4=>1,6=>1,7=>0,8=>1,9=>0,10=>0])
            ],
            [
                'id' => 8,
                'name' => 'Doctor',
                'dashboard' => 'doctor',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,10=>0])
                // only access indoor
            ],
            [
                'id' => 9,
                'name' => 'Receptionist',
                'dashboard' => 'reception',
                'basic_access' => json_encode([1=>0,2=>0,3=>1,4=>1,5=>0,7=>0,8=>0,9=>1,10=>0])
                // Schedule & Services, Indoor readonly but billing full control
            ],
            [
                'id' => 10,
                'name' => 'Nurse',
                'dashboard' => 'nurse',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,10=>0])
                // only access indoor
            ],
            [
                'id' => 11,
                'name' => 'Pharmacist',
                'dashboard' => 'pharmacy',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,9=>0,10=>0])
                // only access pharmacy
            ],
            [
                'id' => 12,
                'name' => 'Laboratorist',
                'dashboard' => 'lab',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,8=>0,9=>0,10=>0])
                // only access lab
            ],
            [
                'id' => 13,
                'name' => 'Reports Delivery',
                'dashboard' => 'lab',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,8=>0,9=>0,10=>0])
                // only access lab
            ],
            [
                'id' => 14,
                'name' => 'Others',
                'dashboard' => 'others',
                'basic_access' => json_encode([1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0])
                // only access lab
            ]
        ]);
        $faker = Faker::create();
        GeneralSettings::insert([
            "user_code" => "PHS",
            "name" => json_encode(["en"=> "Prime Hospital", "bn"=> "প্রাইম হসপিটাল"], JSON_UNESCAPED_UNICODE),
            "address" => json_encode(["en"=> "Bishwa Road, Subhanighat, Sylhet-3100", "bn"=> "বিশ্বরোড, সুবহানীঘাট, সিলেট-৩১০০", "hotline"=>"+8801714820333"], JSON_UNESCAPED_UNICODE),
            "icon" => json_encode(["icon"=> "images/logo.jpg"], JSON_UNESCAPED_UNICODE),
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
