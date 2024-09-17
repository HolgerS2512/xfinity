<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Mail\Auth\RegisterSuccessMail;
use App\Models\User;
use App\Models\Auth\VerifyEmailToken;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Jobs\Auth\SendRegisterSuccessEmail;
use App\Jobs\Auth\SendVerifyEmail;
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
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            // Check if user doesnt exist.
            if (!User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'email_user_exists'],
                ], 400);
            }

            // Hashed request password.
            $input = $request->validated();
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
                    // Mail::to($user->email)->send(new VerifyEmailMail(route('verify_email', [$urlCode]), $token));
                    dispatch(new SendVerifyEmail($user->email, route('verify_email', [$urlCode]), $token));

                    DB::commit();

                    return response()->json([
                        'status' => true,
                        'url' => $urlCode,
                    ], 200);
                }
            }

            DB::rollBack();

            return response()->json([
                'status' => false,
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

    /**
     * Laravel Passport User Verify Email API Function
     * 
     * @param \App\Http\Requests\Auth\VerifyEmailRequest $request
     * @param string $url
     * @return \Illuminate\Http\Response
     */
    public function verifyEmail(VerifyEmailRequest $request, $url)
    {
        DB::beginTransaction();

        try {
            // Search and check in table "verify_email_tokens" via url if is exist.
            $tokenColl = DB::table('verify_email_tokens')->where('url', $url);
            $expireToken = $tokenColl->first();

            if (!$expireToken) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'url_link_not_match'],
                ], 400);
            }

            // Seach user via token relations.
            $user = User::findOrFail($expireToken->user_id);

            if (!$user) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'account_doesnt_exists_verify'],
                ], 400);
            }

            // Check if auth tokens doesnt match.
            if ((int) $request->pin !== $expireToken->token) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'token_not_match'],
                ], 400);
            }

            // Check if auth token expires date doesnt greater then 15 minutes and delete greater then 1 day.
            if (Carbon::now()->diffInMinutes($expireToken->created_at) > 15) {
                DB::table('verify_email_tokens')->where('created_at', '<', Carbon::now()->subWeeks(2))->delete();

                $tokenColl->delete();
                DB::commit();

                return response()->json([
                    'status' => false,
                    'message' => [true, 'token_timeout'],
                ], 400);
            }

            // Delete token, update user and send register success mail.
            $tokenColl->delete();

            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);

            // Send email
            // Mail::to($user->email)->send(new RegisterSuccessMail);
            dispatch(new SendRegisterSuccessEmail($user->email));
            DB::commit();

            return response()->json([
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }
}
