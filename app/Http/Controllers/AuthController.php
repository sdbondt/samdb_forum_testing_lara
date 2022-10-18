<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function signup() {
        $attr = request()->validate([
            'username' => ['required', 'min:1', 'max:255', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'min:7', 'max:255', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/']
        ]);

        $user = User::create($attr);
        $token = $user->createToken('authToken')->plainTextToken;

        return response([
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    public function login() {
        $attr = request()->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);
        $user = User::where('email', request('email'))->first();
        if(!$user) {
            return response()->json('Invalid credentials.', Response::HTTP_UNAUTHORIZED);
        } elseif(!auth()->attempt($attr)) {
            return response()->json('Invalid credentials.', Response::HTTP_UNAUTHORIZED);
        } else {
            $token = $user->createToken('authToken')->plainTextToken;
            return response([
                'token' => $token
            ], Response::HTTP_OK);
        }
    }

    public function logout() {
        request()->user()->currentAccessToken()->delete();
        return response()->json('You are now logged out.', Response::HTTP_OK);
    }

    public function profile() {
        return request()->user()->load('posts', 'activities');
    }
}
