<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    // Fetch all doctors
    $doctors = Doctor::all();

    // Return a JSON response with the list of doctors using the DoctorResource
    return response()->json([
      'success' => true,
      'data' => DoctorResource::collection($doctors)
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Doctor $doctor): JsonResponse
  {
    return response()->json([
      'success' => true,
      'data' => new DoctorResource($doctor)
    ], 200);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Doctor $doctor): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'insurance' => 'nullable|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $doctor->update($request->only(['specialization']));

    return response()->json(['success' => new DoctorResource($doctor)], 200);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
