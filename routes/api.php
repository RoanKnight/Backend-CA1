<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;

Route::controller(AuthController::class)->group(function () {
  Route::post('register', 'register');
  Route::post('login', 'login');
});

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
  Route::apiResource('appointments', AppointmentController::class)->missing(function (Request $request) {
    $response = [
      'success' => false,
      'message' => 'Appointment not found.'
    ];

    return response()->json($response, 404);
  });

  Route::apiResource('doctors', DoctorController::class)->missing(function (Request $request) {
    $response = [
      'success' => false,
      'message' => 'Doctor not found.'
    ];

    return response()->json($response, 404);
  });

  Route::apiResource('patients', PatientController::class)->missing(function (Request $request) {
    $response = [
      'success' => false,
      'message' => 'Patient not found.'
    ];

    return response()->json($response, 404);
  });
});
