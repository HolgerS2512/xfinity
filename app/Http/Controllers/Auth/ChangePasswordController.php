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
use App\Traits\Api\GetApiCodesTrait;

final class ChangePasswordController extends Controller
{
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
                    'message' => (object) ['password' => [__('passwords.third_match')]],
                ], 401);
            }

            // Compares passwords for matches.
            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('passwords.not_match'),
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
            $token = GetApiCodesTrait::create()->token;

            // Delete all coulmns is older than one hour.
            DB::table('password_resets')->where('created_at', '<', Carbon::now()->subHour(1))->delete();

            // Send email.
            Mail::to($request->user()->email)->send(new ChangePasswordMail($token));

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
                'message' => __('passwords.change'),
            ], 200);
        } catch (Exception $e) {

            return response()->json([
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
                    'message' => (object) ['password' => [__('passwords.third_match')]],
                ], 401);
            }

            // Compares passwords for matches.
            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => __('passwords.not_match'),
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
                'message' => __('passwords.reset'),
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
