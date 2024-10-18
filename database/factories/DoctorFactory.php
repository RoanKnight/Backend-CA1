<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
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
      'specialization' => $this->faker->word(),
      'email' => $this->faker->unique()->safeEmail(),
      'phone_number' => $this->faker->phoneNumber(),
    //   'user_id' => User::factory(),
    ];
  }
}
