<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Fetch all patients
    $patients = Patient::all();

    // Create a Faker instance
    $faker = Faker::create();

    // Create an appointment for each patient
    foreach ($patients as $patient) {
      Appointment::factory()->create([
        'at' => $faker->dateTimeBetween(Carbon::now()->addDay(), Carbon::now()->addWeek()),
        'cost' => $faker->randomFloat(2, 0, 100),
        'paid' => $faker->boolean(),
        'patient_id' => $patient->id,
        'doctor_id' => $patient->doctor_id,
      ]);
    }
  }
}
