<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;

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

  // Add route for AuthController index method
  Route::get('users', [AuthController::class, 'index']);
  Route::put('users/update/{id}', [AuthController::class, 'update']);
});
