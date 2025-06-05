<?php

namespace Database\Seeders;

use App\Models\Beds;
use App\Models\Rooms;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['OPD', 'Ward', 'Cabin', 'Lab', 'OT'];

        $roomsToInsert = [];
        for ($floor = 1; $floor <= 5; $floor++) {
            for ($room_number = 1; $room_number <= 3; $room_number++) {
                $room_no = ($floor * 100) + $room_number;
                $roomsToInsert[] = [
                    'type' => $types[array_rand($types)],
                    'room_no' => $room_no,
                    'floor_no' => $floor,
                    'bed_capacity' => rand(1, 3) * 5,
                    'status' => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }
        }

        Rooms::insert($roomsToInsert);

        foreach(range(1, 5) as $i) {
            Beds::insert([
                'rooms_id' => $i %2 == 0 ? 1 : 2,
                'bed_number' => $i,
                'bed_type' => "Big",
                'price' => rand(10 * 50, 1000 * 50),
                'timeline' => rand(1, 2),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
