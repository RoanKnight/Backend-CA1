<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create a specific user for testing
    User::create([
      'name' => 'Test User',
      'email' => 'test.user@example.com',
      'password' => bcrypt('password'),
      'phone_number' => '123-456-7890',
      'address' => '123 Test St',
      'role' => User::ROLE_PATIENT
    ]);

    // Total number of users to create
    $totalUsers = 50;
    $doctorPercentage = 0.2;

    // Calculate the number of doctors to create
    $doctorCount = ($totalUsers * $doctorPercentage);

    // Create users with roles
    User::factory($totalUsers)->create()->each(function ($user, $index) use ($doctorCount) {
      // Assign the doctor role to the first $doctorCount users
      if ($index < $doctorCount) {
        $user->role = User::ROLE_DOCTOR;
      } else {
        // Assign the patient role to the remaining users
        $user->role = User::ROLE_PATIENT;
      }
      $user->save();
    });
  }
}
