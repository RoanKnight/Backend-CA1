<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Http\Resources\AuthResource;
use Validator;

class AuthController extends BaseController
{

  public function index(): JsonResponse
  {
    $users = User::all();

    return response()->json([
      'success' => true,
      'data' => AuthResource::collection($users)
    ], 200);
  }

  public function show(User $user): JsonResponse
  {
    return response()->json([
      'success' => true,
      'data' => new AuthResource($user)
    ], 200);
  }

  public function register(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:6',
      'c_password' => 'required|same:password',
      'phone_number' => 'nullable|string',
      'address' => 'nullable|string',
      'role' => 'nullable|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $input['role'] = $input['role'] ?? User::ROLE_PATIENT;
    $user = User::create($input);
    $success['token'] = $user->createToken('MyApp')->plainTextToken;
    $success['user'] = new AuthResource($user);

    return response()->json(['success' => $success], 200);
  }

  public function login(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
      $user = Auth::user();
      $success['token'] = $user->createToken('MyApp')->plainTextToken;
      $success['user'] = new AuthResource($user);

      return response()->json(['success' => $success], 200);
    } else {
      return response()->json(['error' => 'Unauthorised'], 401);
    }
  }

  public function update(Request $request, User $user): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string',
      'email' => 'required|email|unique:users,email,' . $user->id,
      'phone_number' => 'nullable|string',
      'address' => 'nullable|string',
      'role' => 'nullable|string|in:' . User::ROLE_PATIENT . ',' . User::ROLE_DOCTOR,
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $user->update($request->only(['name', 'email', 'phone_number', 'address', 'role']));

    return response()->json(['success' => new AuthResource($user)], 200);
  }

  public function destroy(User $user): JsonResponse
  {
    $this->updateDeletedStatus($user, true);

    return response()->json(['success' => 'User deleted successfully'], 200);
  }

  public function restore(User $user): JsonResponse
  {
    $this->updateDeletedStatus($user, false);

    return response()->json(['success' => 'User restored successfully'], 200);
  }

  private function updateDeletedStatus(User $user, bool $status): void
  {
    // Update the deleted status for the user
    $user->update(['deleted' => $status]);

    // Check the role of the user and update the corresponding records
    if ($user->role === User::ROLE_DOCTOR) {
      $doctor = Doctor::where('user_id', $user->id)->first();
      if ($doctor) {
        $doctor->update(['deleted' => $status]);
        // Update the deleted status for all appointments related to the doctor
        Appointment::where('doctor_id', $doctor->id)->update(['deleted' => $status]);
      }
    } elseif ($user->role === User::ROLE_PATIENT) {
      $patient = Patient::where('user_id', $user->id)->first();
      if ($patient) {
        $patient->update(['deleted' => $status]);
        // Update the deleted status for all appointments related to the patient
        Appointment::where('patient_id', $patient->id)->update(['deleted' => $status]);
      }
    }
  }
}
