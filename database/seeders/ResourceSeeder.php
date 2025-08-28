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

        $roomsToInsert = [];
        $bedsToInsert = [];

// Floor 1 - Cabins
        foreach (range(1, 3) as $room_number) {
            $room_no = 100 + $room_number; // 101,102,103
            $roomsToInsert[] = [
                'type' => 'Cabin',
                'room_no' => $room_no,
                'floor_no' => 1,
                'bed_capacity' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $bedsToInsert[] = [
                'rooms_id' => null, // will update after rooms inserted
                'bed_number' => 1,
                'bed_type' => 'Special',
                'price' => rand(500, 900),
                'timeline' => 2, // daily
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

// Floor 2 - ICU, CCU, NICU
        $floor2Rooms = ['ICU', 'CCU', 'NICU'];
        foreach ($floor2Rooms as $index => $name) {
            $room_no = 200 + ($index + 1); // 201,202,203
            $bed_capacity = rand(7, 10);
            $roomsToInsert[] = [
                'type' => $name,
                'room_no' => $room_no,
                'floor_no' => 2,
                'bed_capacity' => $bed_capacity,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            for ($i = 1; $i <= $bed_capacity; $i++) {
                $bedsToInsert[] = [
                    'rooms_id' => null, // will update after rooms inserted
                    'bed_number' => $i,
                    'bed_type' => 'Special',
                    'price' => 500, // per hour
                    'timeline' => 1, // hourly
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

// Floor 3 - Wards
        foreach (range(1, 3) as $room_number) {
            $room_no = 300 + $room_number; // 301,302,303
            $bed_capacity = 10;
            $roomsToInsert[] = [
                'type' => 'Ward',
                'room_no' => $room_no,
                'floor_no' => 3,
                'bed_capacity' => $bed_capacity,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            for ($i = 1; $i <= $bed_capacity; $i++) {
                $bedsToInsert[] = [
                    'rooms_id' => null, // will update after rooms inserted
                    'bed_number' => $i,
                    'bed_type' => 'Normal',
                    'price' => rand(200, 500),
                    'timeline' => 2, // daily
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

// Insert rooms and get IDs
        $roomIds = [];
        foreach ($roomsToInsert as $room) {
            $roomIds[] = Rooms::insertGetId($room);
        }

// Assign room IDs to beds
        $bedIndex = 0;
        foreach ($roomsToInsert as $rIndex => $room) {
            $room_id = $roomIds[$rIndex];
            $bed_count = $room['bed_capacity'];

            for ($i = 0; $i < $bed_count; $i++) {
                $bedsToInsert[$bedIndex]['rooms_id'] = $room_id;
                $bedIndex++;
            }
        }

// Insert beds
        Beds::insert($bedsToInsert);
    }
}
