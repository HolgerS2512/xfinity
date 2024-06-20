<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use App\Models\Auth\VerifyEmailToken;
use Exception;
use App\Traits\Api\HasApiUrlCodeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
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
                    Mail::to($user->email)->send(new VerifyEmailMail($verify->id, url('email/verify/'), $urlCode, $accessToken, $logo));

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
     * Laravel Passport User Login  API Function
     * 
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
     * 
     */
    public function verifyEmail(Request $request, $id, $code)
    {
        try {
            // php artisan make:request Auth/VerifyEmailRequest

            return response()->json([
                'request' => $request->all(),
            ]);
            

            // return response()->json([
            //     'check' => Carbon::now()->diffInMinutes() > 10
            // ]);

            // VerifyEmail::toMailUsing(function ($notifiable, $url) {
            //     return (new MailMessage)
            //         ->subject()
            //         ->line(__('auth.verify_line'))
            //         ->action(__('auth.verify_action'), $url);
            // });

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
