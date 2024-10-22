<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;
use App\Models\User;

// Authentication routes
Route::controller(AuthController::class)->group(function () {
  Route::post('register', 'register');
  Route::post('login', 'login');
});

// Route to get the authenticated user
Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  // Appointment routes
  Route::apiResource('appointments', AppointmentController::class)->missing(function (Request $request) {
    return response()->json([
      'success' => false,
      'message' => 'Appointment not found.'
    ], 404);
  });

  // Doctor routes
  Route::apiResource('doctors', DoctorController::class)->missing(function (Request $request) {
    return response()->json([
      'success' => false,
      'message' => 'Doctor not found.'
    ], 404);
  });

  // Patient routes
  Route::apiResource('patients', PatientController::class)->missing(function (Request $request) {
    return response()->json([
      'success' => false,
      'message' => 'Patient not found.'
    ], 404);
  });

  // Routes for the users table
  Route::prefix('users')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->middleware('check.role:' . User::ROLE_DOCTOR);
    Route::get('/{user}', [AuthController::class, 'show']);
    Route::patch('/{user}', [AuthController::class, 'update']);
    Route::delete('/{user}', [AuthController::class, 'destroy']);
    Route::patch('/{user}/restore', [AuthController::class, 'restore']);
  });

  // Routes for the patients table
  Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']);
    Route::get('/{patient}', [PatientController::class, 'show']);
    Route::patch('/{patient}', [PatientController::class, 'update']);
  });

  // Routes for the doctors table
  Route::prefix('doctors')->group(function () {
    Route::get('/', [DoctorController::class, 'index']);
    Route::get('/{doctor}', [DoctorController::class, 'show']);
  });

  // Routes for the appointments table
  Route::prefix('appointments')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::get('/{appointment}', [AppointmentController::class, 'show']);
  });
});
