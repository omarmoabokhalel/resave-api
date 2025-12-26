<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // User Registration
    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'image'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 200);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'image'    => $request->image ?? null,
            'points'   => 0,
            'level'    => 'Bronze',
            'role'     => 'user'
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user
        ], 200);
    }

    // Rider Registration
    public function riderRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:riders,email',
            'password' => 'required|min:6',
            'phone'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 200);
        }

        $rider = Rider::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone
        ]);

        $token = $rider->createToken('rider-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Rider registered successfully',
            'token'   => $token,
            'rider'   => $rider
        ], 200);
    }

    // User Login
  public function userLogin(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed',
            'errors'  => $validator->errors()
        ], 200);
    }

    // جلب الـ user حسب الايميل
    $user = User::where('email', $request->email)->first();

    if (! $user) {
        return response()->json([
            'status'  => 'error',
            'message' => 'User not found'
        ], 200);
    }

    if (! Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Incorrect password'
        ], 200);
    }

    // التحقق من Role
    if (!in_array($user->role, ['user', 'admin'])) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized role'
        ], 200);
    }

//
    // إنشاء Token حسب الـ role
    try {
    $tokenName = $user->role . '-token';
    $token = $user->createToken($tokenName)->plainTextToken;
} catch (\Exception $e) {
    return response()->json([
        'status' => 'error',
        'message' => 'Token creation failed',
        'error' => $e->getMessage()
    ], 500);
}


    return response()->json([
        'status'  => 'success',
        'message' => ucfirst($user->role) . ' logged in successfully',
        'token'   => $token,
        'user'    => $user
    ], 200);
}


    // Rider Login
    public function riderLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 200);
        }

        $rider = Rider::where('email', $request->email)->first();

        if (! $rider) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rider not found'
            ], 200);
        }

        if (! Hash::check($request->password, $rider->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Incorrect password'
            ], 200);
        }

        $token = $rider->createToken('rider-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Rider logged in successfully',
            'token'   => $token,
            'rider'   => $rider
        ], 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
