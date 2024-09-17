<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordEditRequest;
use App\Http\Requests\Auth\ChangePasswordUpdateRequest;
use App\Jobs\Auth\SendChangePasswordEmail;
use App\Mail\Auth\ChangePasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Traits\Api\GetApiCodesTrait;

final class ChangePasswordController extends Controller
{
    /**
     * Laravel Passport User Change Password Edit API Function
     * 
     * @param  \App\Http\Requests\Auth\ChangePasswordEditRequest $request
     * @return \Illuminate\Http\Response
     */
    public function edit(ChangePasswordEditRequest $request)
    {
        DB::beginTransaction();

        try {
            // Compare old password and new password for match.
            if ($request->current_password === $request->new_password) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'password_match_old_new'],
                ], 400);
            }

            // Compares passwords for matches.
            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'password_not_match_db_pwd'],
                ], 400);
            }

            // Check if user exist.
            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'email_doesnt_exists'],
                ], 401);
            }

            // Generate auth token.
            $token = GetApiCodesTrait::create()->token;

            // Delete all coulmns is older than one hour.
            DB::table('password_resets')->where('created_at', '<', Carbon::now()->subHour(1))->delete();

            // Send email.
            // Mail::to($request->user()->email)->send(new ChangePasswordMail($token));
            dispatch(new SendChangePasswordEmail($request->user()->email, $token));

            // Search and check is column exist. Inserted or updated "password_resets" table.
            $resetToken = DB::table('password_resets')->where('email', $request->user()->email)->get();

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

    /**
     * Laravel Passport User Change Password Update API Function
     * 
     * @param  \App\Http\Requests\Auth\ChangePasswordUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ChangePasswordUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            // Compare old password and new password for match.
            if ($request->current_password === $request->new_password) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'password_match_old_new'],
                ], 400);
            }

            // Compares passwords for matches.
            if (!Hash::check($request->current_password, $request->user()->password)) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'password_not_match_db_pwd'],
                ], 401);
            }

            // Check if user exist.
            if (User::where('email', $request->user()->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'email_not_match_db_email'],
                ], 401);
            }

            // Find column in table "password_resets".
            $dbToken = DB::table('password_resets')->where('email', $request->user()->email)->first();

            // Checks for token match.
            if ($dbToken->token !== (int) $request->pin) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'token_not_match'],
                ], 400);
            }

            // Delete token via id.
            DB::table('password_resets')->delete($dbToken->id);

            // Updated user password.
            $user = User::where('email', $request->user()->email);

            $user->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => Carbon::now(),
            ]);

            // $token = $request->user()->token();

            // Delete current user token
            // DB::table('oauth_access_tokens')->delete($token->id);

            // // Delete expired token (older then 3 Months)
            // DB::table('oauth_access_tokens')->where('expires_at', '<', Carbon::now()->subMonths(3))->delete();

            // // Delete old current user tokens
            // $tokens = DB::table('oauth_access_tokens')->where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get();

            // for ($i = 1; $i < count($tokens); $i++) {
            //     DB::table('oauth_access_tokens')->delete($tokens[$i]->id);
            // }

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
