<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Classroom;
use App\Models\Building;
use App\Models\RoomType;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy danh sách ID của buildings và room_types
        $buildings = Building::pluck('id');
        $roomTypes = RoomType::pluck('id');

        for ($i = 0; $i < 10; $i++) {
            Classroom::create([
                'building_id' => $buildings->random(),
                'room_type_id' => $roomTypes->random(),
                'classroom_name' => $faker->name,
            ]);
        }
    }
}
