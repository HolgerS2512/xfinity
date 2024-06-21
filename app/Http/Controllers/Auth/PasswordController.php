<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Auth\ChangePasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Traits\Favicon\Base64Trait;

class PasswordController extends Controller
{
    /**
     * Laravel Passport User Change Password  API Function
     * 
     */
    public function changePassword(Request $request)
    {
        try {
            $credentials = Validator::make($request->all(), [
                'current_password' => 'required|min:8|max:255',
                'password' => 'required|string|min:8|max:255|confirmed|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'password_confirmation' => 'required|string|min:8|max:255|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->errors(),
                ], 401);
            }

            if ($request->current_password === $request->password) {

                return response()->json([
                    'status' => false,
                    'message' => (object) ['password' => [__('auth.pwd_third_match')]],
                ], 401);
            }

            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.pwd_not_match'),
                ], 401);
            }

            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            $token = rand(10, 100000);

            DB::table('password_resets')->insert([
                'email' => $request->user()->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $logo = Base64Trait::getEmailLogo();
            Mail::to($request->user()->email)->send(new ChangePasswordMail($token, $logo));

            return response()->json([
                'status' => true,
                'message' => __('auth.pwd_change'),
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * Laravel Passport User Change Password  API Function
     * 
     */
    public function newPassword(Request $request)
    {
        try {
            $credentials = Validator::make($request->all(), [
                'pin' => 'required|integer|max:100000|min:10',
                'current_password' => 'required|min:8|max:255',
                'password' => 'required|string|min:8|max:255|confirmed|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'password_confirmation' => 'required|string|min:8|max:255|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->errors(),
                ], 401);
            }

            if ($request->current_password === $request->password) {

                return response()->json([
                    'status' => false,
                    'message' => (object) ['password' => [__('auth.pwd_third_match')]],
                ], 401);
            }

            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.pwd_not_match'),
                ], 401);
            }

            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            $dbToken = DB::table('password_resets')->where('email', $request->user()->email)->first();

            if ($dbToken->token !== $request->pin) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.token_not_match'),
                ], 401);
            }

            DB::table('password_resets')->delete($dbToken->id);
            $user = User::where('email', $request->user()->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now(),
            ]);

            $this->logout($user);

            return response()->json([
                'status' => true,
                'message' => __('auth.pwd_new'),
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Laravel Passport User Forget Password  API Function
     * 
     */
    public function forgetPassword(Request $request)
    {
        try {
            $credentials = Validator::make($request->all(), [
                'current_password' => 'required|string|min:8|max:255',
                'password' => 'required|string|min:8|max:255|confirmed',
                'password_confirmation' => 'required|string|min:8|max:255'
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->errors(),
                    'back' => $request->all(),
                ], 401);
            }

            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.pwd_not_match'),
                ], 401);
            }

            return response()->json([
                'status' => $credentials->fails(),
                'status2' => $credentials->errors(),
                'status4' => Hash::check($request->current_password, $request->user()->password),
            ], 200);

            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            $token = rand(10, 100000);

            DB::table('password_resets')->insert([
                // 'email' => $email,
                'token' => $token,
            ]);

            return response()->json([
                'status' => false,
                'message' => __('auth.pwd_forget'),
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Laravel Passport User Reset Password  API Function
     * 
     */
    public function resetPassword(Request $request)
    {
        try {

            return response()->json([
                'status' => false,
                'message' => __('auth.pwd_reset'),
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
    public function logout($user)
    {
        try {
            $token = $user->token();
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
