<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Doctor;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->name(),
      'insurance' => $this->faker->word(),
      'email' => $this->faker->unique()->safeEmail(),
      'phone_number' => $this->faker->phoneNumber(),
      'doctor_id' => Doctor::factory(),
    //   'user_id' => User::factory(),
    ];
  }
}
