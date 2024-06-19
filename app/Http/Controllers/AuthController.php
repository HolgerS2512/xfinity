<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Laravel Passport User Registration  API Function
     */
    public function register(RegisterRequest $request)
    {
        try {
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            $token = $user->createToken('xFinity_ACCESS_TOKEN')->accessToken;

            // EMAIL

            return response()->json([
                'status' => true,
                'message' => __('auth.register'),
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Laravel Passport User Login  API Function
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->input('email'))->first();

            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('xFinity_ACCESS_TOKEN')->accessToken;

                    return response()->json([
                        'status' => true,
                        'message' => __('auth.login'),
                        'token' => $token,
                        'user' => $user,
                    ], 200);
                }
            }

            return response()->json([
                'status' => false,
                'message' => __('passwords.user'),
            ], 422);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Laravel Passport User Logout  API Function
     */

    public function logout(Request $request)
    {
        try {
            $token = $request->user()->token();
            $token->revoke();
    
            return response()->json([
                'status' => true,
                'message' => __('auth.logout'),
            ], 200);
            
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
