<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Http\Resources\AuthResource;
use Validator;

class AuthController extends BaseController
{
  /**
   * Register api
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
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

  /**
   * Login api
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
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

  /**
   * Index api
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(): JsonResponse
  {
    $users = User::all();

    return response()->json([
      'success' => true,
      'data' => AuthResource::collection($users)
    ], 200);
  }


  /**
   * Update user details
   *
   * @param \Illuminate\Http\Request $request
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request, int $id): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string',
      'email' => 'required|email|unique:users,email,' . $id,
      'phone_number' => 'nullable|string',
      'address' => 'nullable|string',
      'role' => 'nullable|string|in:' . User::ROLE_PATIENT . ',' . User::ROLE_DOCTOR,
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $user = User::findOrFail($id);
    $user->update($request->only(['name', 'email', 'phone_number', 'address', 'role']));

    // Update role-specific details
    // if ($user->role === User::ROLE_PATIENT && $user->patient) {
    //   $user->patient->update($request->only(['name', 'email', 'phone_number']));
    // } elseif ($user->role === User::ROLE_DOCTOR && $user->doctor) {
    //   $user->doctor->update($request->only(['name', 'email', 'phone_number']));
    // }

    return response()->json(['success' => new AuthResource($user)], 200);
  }
}
