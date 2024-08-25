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
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

final class AuthController extends Controller
{
    /**
     * The name of the custom authentication cookie used in the application.
     *
     * @var string
     */
    private string $cookieName = 'xFs_at';

    /**
     * User Look up API Function. Decides on registration or login form.
     * 
     * @param \App\Http\Requests\Auth\LookupRequest $request
     * @return \Illuminate\Http\Response
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
                'email' => $request->email,
            ], 200);
        } catch (Exception $e) {
            Log::channel('database')->error('AuthController|lookup: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Laravel Passport User Login  API Function
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\Response
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
                    $token = $user->createToken($this->cookieName)->accessToken;
                    // 60 * 24 * 10  - 10 Days

                    $userAttributes = $user->only(['firstname', 'lastname', 'email']);
                    
                    return response()->json([
                        'status' => true,
                        'message' => __('auth.login'),
                        'token' => $token,
                        'user' => $userAttributes,
                    ], 200);
                }
            }

            return response()->json([
                'status' => false,
                'message' => __('passwords.user'),
            ], 422);
        } catch (Exception $e) {
            Log::channel('database')->error('AuthController|login: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
                'message' =>  __('error.500'),
            ], 500);
        }
    }

    /**
     * Laravel Passport User is not Login API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function unauthenticated()
    {
        return response()->json([
            'status' => false,
            'message' => __('auth.unauthenticated'),
        ], 403);
    }

    /**
     * Laravel Passport User Logout  API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        DB::beginTransaction();

        try {
            $token = $request->user()->token();

            // Delete current user token
            DB::table('oauth_access_tokens')->delete($token->id);

            // Delete expired token (older then 3 Months)
            DB::table('oauth_access_tokens')->where('expires_at', '<', Carbon::now()->subMonths(3))->delete();

            // Delete old current user tokens
            $tokens = DB::table('oauth_access_tokens')->where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get();

            for ($i = 1; $i < count($tokens); $i++) {
                DB::table('oauth_access_tokens')->delete($tokens[$i]->id);
            }

            // Cookie::queue(Cookie::forget($this->cookieName));

            $request->session()->regenerate();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('auth.logout'),
            ], 204);
            // http status 204 -> No Content -> no response!
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('AuthController|logout: ' . $e->getMessage(), ['exception' => $e]);

            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
