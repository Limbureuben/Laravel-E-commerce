<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function generateToken($user)
    {
        $payload = [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'exp' => time() + (7 * 24 * 60 * 60)
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function register(Request $request)
        {
            try {
                $request->validate([
                    'username' => 'required|string|unique:users,username',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6',
                    'confirmPassword' => 'required|string|same:password',
                    'role' => 'in:staff,user'
                ],
                [
                    'username.required' => 'Username is required',
                    'username.unique' => 'Username is already taken',
                    'email.required' => 'Email is required',
                    'email.unique' => 'Email is already taken',
                    'password.required' => 'Password is required',
                    'confirmPassword.required' => 'Please confirm your password',
                    'confirmPassword.same' => 'Passwords do not match',
                    'role.in' => 'Invalid role provided'
                ]);


                $user = User::create([
                    'username' => $request->username,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => $request->role ?? 'user'
                ]);

                return response()->json([
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email
                    ],
                    'token' => $this->generateToken($user)
                ], 201);

            } catch (\Exception $e) {
                Log::error('Registration error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'input' => $request->all(),
                ]);

                return response()->json([
                    'message' => 'Registration failed',
                    'error' => $e->getMessage()  // Optional: remove in production for security
                ], 500);
            }
        }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string'
    //     ]);

    //     $user = User::where('username', $request->username)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Invalid username or password'], 401);
    //     }

    //     return response()->json([
    //         'user' => [
    //             'id' => $user->id,
    //             'username' => $user->username,
    //             'email' => $user->email,
    //             'role' => $user->role
    //         ],
    //         'token' => $this->generateToken($user)
    //     ], 200);
    // }

    public function login(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Find user
        $user = User::where('username', $request->username)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        // Return success
        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $this->generateToken($user)
        ], 200);

    } catch (ValidationException $e) {
        // Validation failure
        return response()->json(['errors' => $e->errors()], 422);

    } catch (\Exception $e) {
        // Log and return generic error
        Log::error('Login Error: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'message' => 'Something went wrong during login.',
            'error' => $e->getMessage() // remove this in production
        ], 500);
    }
}
}
