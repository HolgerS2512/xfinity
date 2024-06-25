<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Mail\Auth\RegisterSuccessMail;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use App\Models\Auth\VerifyEmailToken;
use Exception;
use App\Traits\Api\HasApiUrlCodeTrait;
use App\Traits\Favicon\Base64Trait;
use App\Traits\Auth\Logout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use HasApiUrlCodeTrait, Logout;

    /**
     * Logo as base64 string.
     *
     * @var string
     */
    public $logoBase64;

    /**
     * Sets values.
     *
     */
    public function __construct()
    {
        $this->logoBase64 = Base64Trait::getEmailLogo();
    }

    /**
     * Laravel Passport User Registration  API Function
     * 
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Check if user doesnt exist.
            if (!User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_exists'),
                ], 401);
            }

            // Hashed request password.
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            // Create new User instance and save it.
            $user = new User($input);
            $user->save();

            if ($user) {

                // Create auth token and url.
                $token = rand(10, 100000);
                $urlCode = self::createUrlCode()->urlCode;

                // Save verification model in db.
                $verify = new VerifyEmailToken([
                    'user_id' => $user->id,
                    'token' => $token,
                    'url' => $urlCode,
                ]);
                $verify->save();

                if ($verify) {
                    // Send mail with values.
                    Mail::to($user->email)->send(new VerifyEmailMail(route('verify_email', [$urlCode]), $token, $this->logoBase64));

                    return response()->json([
                        'status' => true,
                        'message' => __('auth.register'),
                        'url' => $urlCode,
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
            ], 500);
        }
    }

    /**
     * Laravel Passport User Verify Email API Function
     * 
     * @param \App\Http\Requests\Auth\VerifyEmailRequest $request
     * @param string $url
     */
    public function verifyEmail(VerifyEmailRequest $request, $url)
    {
        try {
            // Search and check in table "verify_email_tokens" via url if is exist.
            $tokenColl = DB::table('verify_email_tokens')->where('url', $url);
            $expireToken = $tokenColl->first();

            if (!$expireToken) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_url'),
                ], 401);
            }

            // Seach user via token relations.
            $user = User::findOrFail($expireToken->user_id);

            if (!$user) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_user_exist'),
                ], 401);
            }

            // Check if auth tokens doesnt match.
            if ((int) $request->pin !== $expireToken->token) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_pin'),
                ], 401);
            }

            // Check if auth token expires date doesnt greater then 15 minutes.
            if (Carbon::now()->diffInMinutes($expireToken->created_at) > 15) {
                $user->delete();
                $tokenColl->delete();

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_token'),
                ], 408);
            }

            // Delete token, update user and send register success mail.
            $tokenColl->delete();

            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);

            Mail::to($user->email)->send(new RegisterSuccessMail($this->logoBase64));

            return response()->json([
                'status' => true,
                'message' => __('auth.email_verify'),
            ], 200);

        } catch (Exception $e) {

            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Laravel Passport User Login  API Function
     * 
     * @param \App\Mail\Auth\LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        try {
            // Search user and check if email verified.
            $user = User::where('email', $request->input('email'))->first();

            if (Carbon::now()->diffInMinutes($user->email_verified_at) === 0) {
                return response()->json([
                    'status' => true,
                    'message' => __('auth.unauth_mail'),
                ], 408);
            }

            if ($user) {
                // Check passwords match, set access token and login app.
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
            ], 500);
        }
    }
}
