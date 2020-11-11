<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;

class AuthController extends Controller
{

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|string|unique:users,email'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'body' => $user], 200);
    }

      /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return HelperController::api_response_format(401, [], 'Invalid email or password');

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        return response()->json(['message' => 'Logged in successfully','body' => $tokenResult->accessToken],200);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $user=$request->user();
        
        $request->user()->token()->revoke();
        
        return response()->json(['message' => 'Logged out successfully','body' => []],200);
    }
}
