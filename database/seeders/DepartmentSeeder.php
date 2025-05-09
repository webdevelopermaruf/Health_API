<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Insert top-level departments
        $cardiologyId = DB::table('departments')->insertGetId([
            'name' => 'Cardiology',
            'description' => 'Department specializing in heart and cardiovascular conditions.',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $neurologyId = DB::table('departments')->insertGetId([
            'name' => 'Neurology',
            'description' => 'Department focusing on disorders of the nervous system.',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $radiologyId = DB::table('departments')->insertGetId([
            'name' => 'Radiology',
            'description' => 'Department providing medical imaging services.',
            'parent_dept' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert sub-departments
        DB::table('departments')->insert([
            [
                'name' => 'Pediatric Cardiology',
                'description' => 'Subspecialty of Cardiology focusing on heart conditions in children.',
                'parent_dept' => $cardiologyId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Neurosurgery',
                'description' => 'Surgical subspecialty for neurological conditions.',
                'parent_dept' => $neurologyId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Interventional Radiology',
                'description' => 'Subspecialty of Radiology using imaging for minimally invasive procedures.',
                'parent_dept' => $radiologyId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Department specializing in musculoskeletal system disorders.',
                'parent_dept' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency Medicine',
                'description' => 'Department handling acute and life-threatening conditions.',
                'parent_dept' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Department specializing in medical care for children.',
                'parent_dept' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oncology',
                'description' => 'Department focusing on cancer diagnosis and treatment.',
                'parent_dept' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
