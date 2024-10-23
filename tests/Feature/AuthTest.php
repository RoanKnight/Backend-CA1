<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;

class AuthTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  public function test_user_register(): void
  {
    $user = $this->getUserData();
    $response = $this->postJson('/api/register', $user);

    $response->assertStatus(200);
    $response->assertJsonStructure($this->getSuccessJsonStructure());

    $this->assertUserRegistered($response, $user);
  }

  public function test_user_register_error(): void
  {
    $user = $this->getUserData(['c_password' => 'mysecret2']);
    $response = $this->postJson('/api/register', $user);

    $response->assertStatus(400);
    $response->assertJsonStructure(['error' => ['c_password']]);

    $this->assertDatabaseMissing('users', [
      'name' => $user['name'],
      'email' => $user['email']
    ]);
  }

  public function test_user_login(): void
  {
    $password = 'mysecret';
    $user = User::factory()->create(['password' => bcrypt($password)]);
    $response = $this->postJson('/api/login', [
      'email' => $user->email,
      'password' => $password
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure($this->getSuccessJsonStructure());

    $this->assertUserLoggedIn($response, $user);
  }

  public function test_user_login_error(): void
  {
    $user = User::factory()->create();
    $response = $this->postJson('/api/login', [
      'email' => $user->email,
      'password' => 'mysecret2'
    ]);

    $response->assertStatus(401);
    $response->assertJsonStructure(['error']);
  }

  public function test_user_info(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/user');
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'id',
      'name',
      'email',
      'email_verified_at',
      'created_at',
      'updated_at'
    ]);
    $this->assertEquals($response->json('name'), $user->name);
    $this->assertEquals($response->json('email'), $user->email);
  }

  public function test_index(): void
  {
    $user = User::factory()->create(['role' => User::ROLE_DOCTOR]);
    User::factory()->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/users');

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'success',
      'data' => [
        '*' => [
          'id',
          'name',
          'email',
          'phone_number',
          'address',
          'role'
        ]
      ]
    ]);
  }

  public function test_show(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/users/' . $user->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'success',
      'data' => [
        'id',
        'name',
        'email',
        'phone_number',
        'address',
        'role'
      ]
    ]);
  }

  public function test_update(): void
  {
    $user = User::factory()->create();
    $updatedData = [
      'name' => 'Updated Name',
      'email' => 'updated@example.com',
      'phone_number' => '1234567890',
      'address' => 'Updated Address',
      'role' => User::ROLE_DOCTOR
    ];

    $response = $this->actingAs($user)->patchJson('/api/users/' . $user->id, $updatedData);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'success' => [
        'id',
        'name',
        'email',
        'phone_number',
        'address',
        'role'
      ]
    ]);

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'name' => 'Updated Name',
      'email' => 'updated@example.com',
      'phone_number' => '1234567890',
      'address' => 'Updated Address',
      'role' => User::ROLE_DOCTOR
    ]);
  }

  public function test_destroy(): void
  {
    $admin = User::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_PATIENT]);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $appointment = Appointment::factory()->create(['doctor_id' => 1, 'patient_id' => $patient->id]);

    $response = $this->actingAs($admin)->deleteJson('/api/users/' . $user->id);

    $response->assertStatus(200);
    $response->assertJson(['success' => 'User deleted successfully']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted' => true]);
    $this->assertDatabaseHas('patients', ['id' => $patient->id, 'deleted' => true]);
    $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'deleted' => true]);
  }

  public function test_restore(): void
  {
    $admin = User::factory()->create();
    $user = User::factory()->create(['role' => User::ROLE_PATIENT, 'deleted' => true]);
    $patient = Patient::factory()->create(['user_id' => $user->id, 'deleted' => true]);
    $appointment = Appointment::factory()->create(['doctor_id' => 1, 'patient_id' => $patient->id, 'deleted' => true]);

    $response = $this->actingAs($admin)->patchJson('/api/users/' . $user->id . '/restore');

    $response->assertStatus(200);
    $response->assertJson(['success' => 'User restored successfully']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted' => false]);
    $this->assertDatabaseHas('patients', ['id' => $patient->id, 'deleted' => false]);
    $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'deleted' => false]);
  }

  private function getUserData(array $overrides = []): array
  {
    return array_merge([
      'name' => $this->faker->name,
      'email' => $this->faker->unique()->safeEmail,
      'password' => 'mysecret',
      'c_password' => 'mysecret'
    ], $overrides);
  }

  private function getSuccessJsonStructure(): array
  {
    return [
      'success' => [
        'token',
        'user' => [
          'id',
          'name',
          'email',
          'phone_number',
          'address',
          'role'
        ]
      ]
    ];
  }

  private function assertUserRegistered($response, $user): void
  {
    $success = $response->json('success');
    $token = $response->json('success.token');
    $name = $response->json('success.user.name');

    $this->assertEquals($name, $user['name']);
    $this->assertNotNull($token);

    $this->assertDatabaseHas('users', [
      'name' => $user['name'],
      'email' => $user['email'],
      'role' => User::ROLE_PATIENT
    ]);
  }

  private function assertUserLoggedIn($response, $user): void
  {
    $success = $response->json('success');
    $token = $response->json('success.token');
    $name = $response->json('success.user.name');

    $this->assertEquals($name, $user->name);
    $this->assertNotNull($token);
  }
}
