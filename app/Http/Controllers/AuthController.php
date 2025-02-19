<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * login in post
     */
    public function login_get()
    {
        return response()->json([
            'ok' => false,
            'status' => 400,
            'message' => 'Unauthorized',
        ]); // return 400 unauthorized if a user tries to access this route without being logged in
    }

    /**
     * signup in post
     */

     public function login_post(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([[
                'ok' => true,
                'user' => $user,
                'token' => $token,
            ],
                'status' => 200,
            ]); // return 200 if the user is logged in
        }
        return response()->json([
            'ok' => false,
            'status' => 400,
            'message' => 'Unauthorized',
        ]); // return 400 unauthorized if a user tries to access this route without being logged in
     }

     /**
     * signup in post
     */

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'ok' => true,
            'user' => $user,
            'token' => $token,
            'status' => 201,
        ]); // return 201 if the user is created
    }

    /**
     * logout in post
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'ok' => true,
            'status' => 200,
            'message' => 'Logged out successfully',
        ]); // return 200 if the user is logged out
    }

}