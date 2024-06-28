<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LookupRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Traits\Auth\Logout;

final class AuthController extends Controller
{
    use Logout;

    /**
     * User Look up API Function. Decides on registration or login form.
     * 
     * @param \App\Http\Requests\Auth\LookupRequest $request
     */
    public function lookup(LookupRequest $request)
    {
        try {
            // Check if user not exist.
            if (User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'route' => 'register',
                ], 200);
            }

            return response()->json([
                'route' => 'login',
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Laravel Passport User Login  API Function
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        try {
            // Search user and check if email verified.
            $user = User::where('email', $request->input('email'))->first();

            if (Carbon::now()->diffInMinutes($user->email_verified_at) === 0) {
                return response()->json([
                    'status' => false,
                    'message' => __('auth.unauth_mail'),
                ], 408);
            }

            if ($user) {
                // Check passwords match, set access token and login app.
                if (Hash::check($request->password, $user->password)) {
                    $request->session()->regenerate();
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

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Laravel Passport User is not Login API Function
     * 
     */
    public function unauthenticated()
    {
        return response()->json([
            'status' => false,
            'message' => __('auth.unauthenticated'),
        ], 403);
    }
}
