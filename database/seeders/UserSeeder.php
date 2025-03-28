<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Role;
use App\Models\RepairTeam;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $roles = Role::pluck('id');
        $repairTeams = RepairTeam::pluck('id');

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'role_id' => $roles->random(),
                'repair_team_id' => $repairTeams->random(),
                'username' => $faker->username,

                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'gender' => $faker->randomElement(['male', 'female']),
                'avatar' => $faker->imageUrl(),
                'password' => $faker->password,
            ]);
        }
    }
}


