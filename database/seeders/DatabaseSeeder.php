<?php

namespace Database\Seeders;

use App\Models\Auth;
use App\Models\GeneralSettings;
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
            'role' => 1,
            'department_id' => 2, // Assuming department ID exists (e.g., nurse)
            'address' => '123 Main St, City, Country',
            'dob' => '1990-05-15',
            'blood' => 'O+',
            'gender' => 'male',
            'status' => true,
            'permission' => null, // JSON permissions
        ]);

        $faker = Faker::create();
        GeneralSettings::insert([
            "user_code" => "demo",
            "name" => json_encode(["en"=> "Prime Hospital", "bn"=> "প্রাইম হসপিটাল"], JSON_UNESCAPED_UNICODE),
            "icon" => json_encode(["icon"=> ""]),
            "sms_api" => json_encode(["balance"=> 0, "rate"=> 0.35, "apikey"=> "8XYpeBwf0JMssLw3Cyxf"]),
            "attendance" => json_encode(["area_code"=>"BRC","area_id"=> 2, "server"=>"http://localhost:9090",
                "username"=>"BRC_College","password"=>"3S1afTdU9cZdT"]),
            "sms_rules" => json_encode([]),
            "payroll_rules" => json_encode([]),
        ]);


    }
}
