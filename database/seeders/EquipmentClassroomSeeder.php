<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Equipment;
use App\Models\Classroom;
use App\Models\EquipmentClassroom;

class EquipmentClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $equipments = Equipment::pluck('id');
        $classrooms = Classroom::pluck('id');

        for ($i = 0; $i < 10; $i++) {
            EquipmentClassroom::create([
                'equipment_id' => $equipments->random(),
                'classroom_id' => $classrooms->random(),
                'quantity' => $faker->numberBetween(1, 10)
            ]);
        }
    }
}
