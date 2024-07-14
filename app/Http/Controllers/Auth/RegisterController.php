<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Mail\Auth\RegisterSuccessMail;
use App\Models\User;
use App\Models\Auth\VerifyEmailToken;
use App\Http\Requests\Auth\VerifyEmailRequest;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\Api\GetApiCodesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

final class RegisterController extends Controller
{
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
                $token = GetApiCodesTrait::create()->token;
                $urlCode = GetApiCodesTrait::create()->url;

                // Save verification model in db.
                $verify = new VerifyEmailToken([
                    'user_id' => $user->id,
                    'token' => $token,
                    'url' => $urlCode,
                ]);
                $verify->save();

                if ($verify) {
                    // Send mail with values.
                    Mail::to($user->email)->send(new VerifyEmailMail(route('verify_email', [$urlCode]), $token));

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

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
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

            // Check if auth token expires date doesnt greater then 15 minutes and delete greater then 1 day.
            if (Carbon::now()->diffInMinutes($expireToken->created_at) > 15) {
                DB::table('verify_email_tokens')->where('expires_at', '<', Carbon::now()->subHours(12))->delete();

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

            Mail::to($user->email)->send(new RegisterSuccessMail);

            return response()->json([
                'status' => true,
                'message' => __('auth.email_verify'),
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}
