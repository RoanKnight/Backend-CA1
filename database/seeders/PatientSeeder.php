<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;
use App\Models\Doctor;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Fetch all users with the patient role
    $users = User::where('role', User::ROLE_PATIENT)->get();

    // Fetch all doctor IDs
    $doctorIds = Doctor::pluck('id')->toArray();

    // Create a Faker instance
    $faker = Faker::create();

    // Create a Patient entry for each user with the patient role
    foreach ($users as $user) {
      // Select a random doctor ID from the fetched IDs
      $doctorId = $faker->randomElement($doctorIds);

      Patient::factory()->create([
        'insurance' => $faker->word(),
        'doctor_id' => $doctorId,
        'user_id' => $user->id,
      ]);
    }
  }
}
