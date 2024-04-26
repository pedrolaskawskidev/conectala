<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::attempt($request->only('email', 'password'))) {
                $token = $request->user()->createToken('incoming')->plainTextToken;

                return response()->json([
                    'message' => 'Autorizado',
                    'token' => $token
                ], 200);
            }
        }
        return response()->json([
            'message' => 'Nao Autorizado',
        ], 403);
    }
}
