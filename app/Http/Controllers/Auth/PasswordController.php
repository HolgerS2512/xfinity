<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\Auth\ChangePasswordMail;
use App\Mail\Auth\ForgetPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Traits\Favicon\Base64Trait;
use App\Traits\Auth\Logout;
use App\Traits\Api\HasApiUrlCodeTrait;

class PasswordController extends Controller
{
    use Logout, HasApiUrlCodeTrait;

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
     * Laravel Passport User Change Password  API Function
     * 
     */
    public function edit(Request $request)
    {
        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'current_password' => 'required|min:8|max:255',
                'password' => 'required|string|min:8|max:255|confirmed|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'password_confirmation' => 'required|string|min:8|max:255|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->errors(),
                ], 400);
            }

            // Compare old password and new password for match.
            if ($request->current_password === $request->password) {

                return response()->json([
                    'status' => false,
                    'message' => (object) ['password' => [__('auth.pwd_third_match')]],
                ], 401);
            }

            // Compares passwords for matches.
            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.pwd_not_match'),
                ], 401);
            }

            // Check if user exist.
            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            // Generate auth token.
            $token = rand(10, 100000);

            // Delete all coulmns is older than one hour.
            DB::table('password_resets')->where('created_at', '<', Carbon::now()->subHour(1))->delete();

            // Send email.
            Mail::to($request->user()->email)->send(new ChangePasswordMail($token, $this->logoBase64));

            // Search and check is column exist. Inserted or updated "password_resets" table.
            $resetToken = DB::table('password_resets')->where('email', $request->email)->get();

            $values = [
                'email' => $request->user()->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ];

            if (boolval(count($resetToken))) {
                DB::table('password_resets')->update($values);
            } else {
                DB::table('password_resets')->insert($values);
            }

            return response()->json([
                'status' => true,
                'message' => __('auth.pwd_change'),
            ], 200);
        } catch (Exception $e) {

            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Laravel Passport User Change Password  API Function
     * 
     */
    public function update(Request $request)
    {
        try {
            // Validation.
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
                ], 400);
            }

            // Compare old password and new password for match.
            if ($request->current_password === $request->password) {

                return response()->json([
                    'status' => false,
                    'message' => (object) ['password' => [__('auth.pwd_third_match')]],
                ], 401);
            }

            // Compares passwords for matches.
            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.pwd_not_match'),
                ], 401);
            }

            // Check if user exist.
            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            // Find column in table "password_resets".
            $dbToken = DB::table('password_resets')->where('email', $request->user()->email)->first();

            // Checks for token match.
            if ($dbToken->token !== (int) $request->pin) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.token_not_match'),
                ], 401);
            }

            // Delete token via id.
            DB::table('password_resets')->delete($dbToken->id);

            // Updated user password.
            $user = User::where('email', $request->user()->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now(),
            ]);

            $this->logout($request);

            return response()->json([
                'status' => true,
                'message' => __('auth.pwd_new'),
            ], 200);
        } catch (Exception $e) {

            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Laravel Passport User Forget Password  API Function
     * 
     * @param \App\Mail\Auth\ForgetRequest $request
     */
    public function forget(ForgetRequest $request)
    {
        try {
            // Check if user exist.
            if (User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            // Generate token and url.
            $token = rand(10, 100000);
            $urlCode = self::createUrlCode()->urlCode;

            // Send email.
            Mail::to($request->email)->send(new ForgetPasswordMail(route('reset_password', [$urlCode]), $token, $this->logoBase64));

            // Delete all coulmns is older than one hour.
            DB::table('password_resets')->where('created_at', '<', Carbon::now()->subHour(1))->delete();

            // Search and check is column exist. Inserted or updated "password_resets" table.
            $resetToken = DB::table('password_resets')->where('email', $request->email)->get();

            $values = [
                'email' => $request->email,
                'token' => $token,
                'url' => $urlCode,
                'created_at' => Carbon::now(),
            ];

            if (boolval(count($resetToken))) {
                DB::table('password_resets')->update($values);
            } else {
                DB::table('password_resets')->insert($values);
            }

            return response()->json([
                'status' => true,
                'message' => __('auth.pwd_forget'),
                'url' => $urlCode,
            ], 200);
        } catch (Exception $e) {

            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Laravel Passport User Reset Password  API Function
     * 
     * @param \App\Http\Requests\Auth\ResetPasswordRequest $request
     * @param string $url
     */
    public function reset(ResetPasswordRequest $request, $url)
    {
        try {
            // Find column in table "password_resets".
            $dbToken = DB::table('password_resets')->where('url', $url)->first();

            // Checks url if exist in table.
            if (is_null($dbToken)) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_url'),
                ], 401);
            }

            // Checks for token doesnt match.
            if ($dbToken->token !== (int) $request->pin) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.token_not_match'),
                ], 401);
            }

            // Check if user exist.
            if (User::where('email', $dbToken->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_not_exists'),
                ], 401);
            }

            // Check that the token column “created_at” is not older than 15 minutes.
            if (Carbon::now()->diffInMinutes($dbToken->created_at) > 15) {
                DB::table('password_resets')->delete($dbToken->id);

                return response()->json([
                    'status' => false,
                    'message' => __('auth.auth_token'),
                ], 408);
            }

            // Updated user password.
            $user = User::where('email', $dbToken->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now(),
            ]);

            // Delete token via id.
            DB::table('password_resets')->delete($dbToken->id);

            return response()->json([
                'status' => true,
                'message' => __('auth.pwd_reset'),
            ], 200);
        } catch (Exception $e) {

            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
