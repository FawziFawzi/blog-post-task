<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register Method
    public function register(Request $request)
    {
        //// Validating user's inputs ////
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8|confirmed'
        ]);

        //After validation succeeded Store the user in the database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Creating a token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User Registered Successfully!',
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }


    //// Login Method ////
    public function Login(Request $request)
    {
        //Validating credentials
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8'
        ]);

        //Retrieving User Data
        $user = User::where('email',$request->email)->first();

        //check the credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'error' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Creating a token
        $token = $user->createToken('auth-token')->plainTextToken;

        //Return token with accepted response
        return response()->json([
            'message' => 'User Login Successfully!',
            'user' => new UserResource($user),
            'token' => $token,
        ],200);

    }

    // Show user data

    public function show(Request $request)
    {
        return response()->json([
            'message' => 'User Data Retrieved Successfully!',
            'user' => new UserResource($request->user()),
        ],200);
    }

    // Logout The User
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User logged out successfully']);
    }
}
