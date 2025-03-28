<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Assignment;


class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
       

        for ($i = 0; $i < 10; $i++) {
            Assignment::create([
               'assignment_time' => $faker->dateTimeBetween('-1 year', 'now'),
               'description' => $faker->text
            ]);
        }
    }
}
