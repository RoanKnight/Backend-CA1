<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Patient;
use App\Models\Doctor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'at' => $this->faker->dateTime(),
      'cost' => $this->faker->randomFloat(2, 0, 100),
      'paid' => $this->faker->boolean(),
      'patient_id' => Patient::factory(),
      'doctor_id' => Doctor::factory(),
    ];
  }
}
