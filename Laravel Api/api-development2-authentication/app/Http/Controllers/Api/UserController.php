<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData =  $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'], //|unique:users
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = User::create($validatedData);

        $token = $user->createToken('Auth_Token')->accessToken;

        return response()->json([
            'token' => $token, 'user' => $user, 'message' => 'user registered successfully', 'status' => 1
        ], 200);
    }
   
    public function login(Request $request)
    {
        $validatedData =  $request->validate([
            'email' => 'required', //|unique:users
            'password' => 'required',
        ]);
        $user = User::where(['email' => $validatedData['email'], 'password' => $validatedData['password']])->first();

        $token = $user->createToken('Auth_Token')->accessToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Login success',
            'status' => 1
        ], 200);
    }
    public function getUser($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'message' => 'user not found',
                'status' => 0
            ], 400);
        } else {
            return response()->json([
                'message' => 'user found',
                'status' => 1,
                'user' => $user
            ], 200);
        }
    }
}
// Hash::check($validatedData['password'],'password')
