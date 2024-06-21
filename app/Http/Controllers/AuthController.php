<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use App\Models\Auth\VerifyEmailToken;
use Exception;
use App\Traits\Api\HasApiUrlCodeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use HasApiUrlCodeTrait;

    /**
     * Laravel Passport User Registration  API Function
     * 
     */
    public function register(RegisterRequest $request)
    {
        try {
            if (!User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_exists'),
                ], 401);
            }

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = new User($input);
            $user->save();

            if ($user) {

                $token = $user->createToken('xFinity_ACCESS_TOKEN')->accessToken;

                $accessToken = rand(10, 100000);
                $urlCode = self::createUrlCode()->urlCode;

                $verify = new VerifyEmailToken([
                    'user_id' => $user->id,
                    'token' => $accessToken,
                    'url' => $urlCode,
                ]);
                $verify->save();

                if ($verify) {

                    $logo = $this->base64Logo();
                    Mail::to($user->email)->send(new VerifyEmailMail(route('verify', [$urlCode]), $accessToken, $logo));

                    return response()->json([
                        'token' => $token,
                        'url' => $urlCode,
                    ], 200);

                    return response()->json([
                        'status' => true,
                        'message' => __('auth.register'),
                        'token' => $token,
                        'user' => $user,
                    ], 200);
                }
            }
            return response()->json([
                'status' => false,
                'message' => __('errors.500'),
            ], 500);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Laravel Passport User Verify Email API Function
     * 
     */
    public function verifyEmail(VerifyEmailRequest $request, $code)
    {
        try {

            $tokenColl = DB::table('verify_email_tokens')->where('url', $code);
            $expireToken = $tokenColl->first();

            if (!$expireToken) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_url'),
                ], 401);
            }

            $user = User::findOrFail($expireToken->user_id);

            if (!$user) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_user_exist'),
                ], 401);
            }

            if ((int) $request->pin !== $expireToken->token) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_pin'),
                ], 401);
            }

            if (Carbon::now()->diffInMinutes($expireToken->created_at) > 10) {
                $user->delete();
                $tokenColl->delete();

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_token'),
                ], 401);
            }

            $tokenColl->delete();

            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => __('auth.email_verify'),
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
     * 
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->input('email'))->first();

            if (Carbon::now()->diffInMinutes($user->email_verified_at) === 0) {
                return response()->json([
                    'status' => true,
                    'message' => __('auth.unauth_mail'),
                ], 401);
            }

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
     * 
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

    /**
     * Returned the logo as base64 string.
     * 
     */
    public function base64Logo()
    {
        return 'data:image/' . 'png' . ';base64,' . base64_encode(file_get_contents(public_path('favicons/logo.png')));
    }
}
