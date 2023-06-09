<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $req): \Illuminate\Http\JsonResponse
    {
        $validate = $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credential = $req->only('email', 'password');

        if(auth()->attempt($credential)) {
            $user = auth()->user();
            $token = $user->createToken('token-name')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout(Request $req): \Illuminate\Http\JsonResponse
    {
        $req->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
