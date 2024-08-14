<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        if(!Auth::attempt($request->all())) {
            return response()->json([
                'message' => "Email or password incorrect"
            ], 401);
        }

        $token =auth()->user()->createToken('auth')->plainTextToken;
    
        return response()->json([
            'message' => "Login success",
            'user' => [
                "id" => auth()->user()->id,
                "email" => auth()->user()->email,
                "accessToken" => $token,
            ]
        ]);
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => "Logout success"
        ]);
    }
}
