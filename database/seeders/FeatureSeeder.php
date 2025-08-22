<?php

namespace Database\Seeders;

use App\Models\Features;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
                [
                    'id' => 1,
                    'name' => 'App', // settings, users (access control), department
                    'path' => '/app',
                    'status' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'HR Manager', // Doctors, Staffs, Recruitment (Job Post, Applicants)
                    'path' => '/hr',
                    'status' => 1,
                ],
                [
                    'id' => 3,
                    'name' => 'Scheduler Manager',
                    'path' => '/scheduler',
                    'status' => 1,
                ],
                [
                    'id' => 4,
                    'name' => 'Service Manager',
                    'path' => '/service-manager',
                    'status' => 1,
                ],
                [
                    'id' => 5,
                    'name' => 'Accounting Manager',
                    'path' => '/accounting',
                    'status' => 1,
                ],
                [
                    'id' => 6,
                    'name' => 'Billing Manager',
                    'path' => '/billing',
                    'status' => 1,
                ],
                [
                    'id' => 7,
                    'name' => 'Lab & Diagnostics',
                    'path' => '/lab-diagnostics',
                    'status' => 1,
                ],
                [
                    'id' => 8,
                    'name' => 'Pharmacy',
                    'path' => '/pharmacy',
                    'status' => 1,
                ]
            ];
        $children = [
            [
                'id' => 1001,
                'name' => 'Users',
                'path' => '/app/users',
                'parent' => 1,
                'status' => 1,
            ],
            [
                'id' => 1002,
                'name' => 'Departments',
                'path' => '/app/departments',
                'parent' => 1,
                'status' => 1,
            ],
            [
                'id' => 2001,
                'name' => 'Doctors',
                'path' => '/hr/doctors',
                'parent' => 2,
                'status' => 1,
            ],
            [
                'id' => 2002,
                'name' => 'Add Doctors',
                'path' => '/hr/doctors/add',
                'parent' => 2001,
                'status' => 1,
            ],
            [
                'id' => 2003,
                'name' => 'Edit Doctors',
                'path' => '/hr/doctors/edit',
                'parent' => 2001,
                'status' => 1,
            ],
            [
                'id' => 2004,
                'name' => 'Staffs',
                'path' => '/hr/staffs',
                'parent' => 2,
                'status' => 1,
            ],
            [
                'id' => 2005,
                'name' => 'Add Staffs',
                'path' => '/hr/staffs/add',
                'parent' => 2004,
                'status' => 1,
            ],
            [
                'id' => 2006,
                'name' => 'Edit Staffs',
                'path' => '/hr/staffs/edit',
                'parent' => 2004,
                'status' => 1,
            ],
            [
                'id' => 2007,
                'name' => 'Recruitment',
                'path' => '/hr/recruitment',
                'parent' => 2,
                'status' => 1,
            ],
            [
                'id' => 2008,
                'name' => 'Job Posting',
                'path' => '/hr/recruitment/job-posting',
                'parent' => 2007,
                'status' => 1,
            ],
            [
                'id' => 2009,
                'name' => 'Job Application',
                'path' => '/hr/recruitment/job-applications',
                'parent' => 2007,
                'status' => 1,
            ],
            [
                'id' => 3001,
                'name' => 'Appointment Scheduler',
                'path' => '/scheduler/appointment',
                'parent' => 3,
                'status' => 1,
            ],
            [
                'id' => 3002,
                'name' => 'Roster Duty Manager',
                'path' => '/scheduler/roster',
                'parent' => 3,
                'status' => 1,
            ],
            [
                'id' => 4001,
                'name' => 'Services',
                'path' => '/service-manager/services',
                'parent' => 4,
                'status' => 1,
            ],
            [
                'id' => 4002,
                'name' => 'Resources',
                'path' => '/service-manager/resources',
                'parent' => 4,
                'status' => 1,
            ],
            [
                'id' => 4003,
                'name' => 'Room Manager',
                'path' => '/service-manager/resources/rooms',
                'parent' => 4002,
                'status' => 1,
            ],
            [
                'id' => 4004,
                'name' => 'Bed Manager',
                'path' => '/service-manager/resources/beds',
                'parent' => 4002,
                'status' => 1,
            ],

            // billing,
            [
            'id' => 6001,
            'name' => 'Outdoor Billing',
            'path' => '/billing/outdoor',
            'parent' => 6,
            'status' => 1,
            ],
            [
            'id' => 6002,
            'name' => 'Indoor Billing',
            'path' => '/billing/indoor',
            'parent' => 6,
            'status' => 1,
            ],

            // lab reports
            [
                'id' => 7001,
                'name' => 'Lab Report',
                'path' => '/lab-diagnostics/lab-reports',
                'parent' => 7,
                'status' => 1,
            ],
            [
                'id' => 7002,
                'name' => 'Lab Delivery',
                'path' => '/lab-diagnostics/lab-delivery',
                'parent' => 7,
                'status' => 1,
            ],

            // pharmacy
            [
                'id' => 8001,
                'name' => 'Medicines',
                'path' => '/pharmacy/medicine',
                'parent' => 8,
                'status' => 1,
            ],
            [
                'id' => 8002,
                'name' => 'Suppliers',
                'path' => '/pharmacy/suppliers',
                'parent' => 8,
                'status' => 1,
            ],
            [
                'id' => 8003,
                'name' => 'Purchases',
                'path' => '/pharmacy/purchases',
                'parent' => 8,
                'status' => 1,
            ],
            [
                'id' => 8004,
                'name' => 'Sales',
                'path' => '/pharmacy/sales',
                'parent' => 8,
                'status' => 1,
            ],
        ];

        Features::where('status', 1)->delete();
        Features::insert($parents);
        Features::insert($children);
    }
}
