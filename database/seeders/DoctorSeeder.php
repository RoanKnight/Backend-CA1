<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\User;
use Faker\Factory as Faker;

class DoctorSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Fetch all users with the doctor role
    $users = User::where('role', User::ROLE_DOCTOR)->get();

    // Create a Faker instance
    $faker = Faker::create();

    // Create a Doctor entry for each user with the doctor role
    foreach ($users as $user) {
      Doctor::factory()->create([
        'name' => $user->name,
        'specialization' => $faker->word(),
        'email' => $user->email,
        'phone_number' => $user->phone_number ?? $faker->phoneNumber(),
      ]);
    }
  }
}
