<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Insert top-level departments
        $administration = DB::table('departments')->insertGetId([
            'name' => 'Administration & Governance',
            'description' => 'Chairman, Managing Directors, Shareholders etc.',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $operations = DB::table('departments')->insertGetId([
            'name' => 'Operations & Management',
            'description' => 'Accounts, Billing, Reception, IT, HR, General Management',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $health = DB::table('departments')->insertGetId([
            'name' => 'Clinical & Medical',
            'description' => 'Medical, Surgical, Emergency, ICU, Outpatient, Inpatient',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $others = DB::table('departments')->insertGetId([
            'name' => 'Facility & Support',
            'description' => 'Pharmacy, Diagnostic/Laboratory, Canteen, Maintenance, Home Service etc.',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert sub-departments
        DB::table('departments')->insert([
            [
                'name' => 'General',
                'description' => 'Medicine & Surgery Department',
                'parent_dept' => $health,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency',
                'description' => 'Emergency Department',
                'parent_dept' => $health,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Critical Care',
                'description' => 'CCU ICU NICU Department',
                'parent_dept' => $health,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
