<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Classroom;
use App\Models\IncidentReport;
use App\Models\EquipmentClassroom;
class IncidentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::pluck('id');
        $equipment_classrooms = EquipmentClassroom::pluck('id');

        for ($i = 0; $i < 10; $i++) {
            IncidentReport::create([
                'user_id' => $users->random(),
                'equipment_classroom_id' => $equipment_classrooms->random(),
                'report_time' => $faker->dateTimeBetween('-1 year', 'now'),
                'description' => $faker->text,
                'status' => $faker->randomElement(['pending', 'completed', 'cancelled'])
            ]);
        }
    }
}
