<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PatientResource;

class PatientController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    // Fetch all patients
    $patients = Patient::all();

    // Return a JSON response with the list of patients using the PatientResource
    return response()->json([
      'success' => true,
      'data' => PatientResource::collection($patients)
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Patient $patient): JsonResponse
  {
    return response()->json([
      'success' => true,
      'data' => new PatientResource($patient)
    ], 200);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Patient $patient): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'insurance' => 'nullable|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $patient->update($request->only(['insurance']));

    return response()->json(['success' => new PatientResource($patient)], 200);
  }
}
