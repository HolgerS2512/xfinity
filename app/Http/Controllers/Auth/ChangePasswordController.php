<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\Auth\SendChangePasswordEmail;
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
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ChangePasswordController extends Controller
{
    /**
     * Laravel Passport User Change Password  API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'current_password' => 'required|min:8|max:255',
                'password' => 'required|string|min:8|max:255|confirmed|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/',
                'password_confirmation' => 'required|string|min:8|max:255|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/'
            ]);

            if ($credentials->fails()) {
  
                return response()->json([
                    'status' => false,
                    // 'message' => $credentials->messages()->all(),
                ], 400);
            }

            // Compare old password and new password for match.
            if ($request->current_password === $request->password) {

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
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangePasswordController|edit: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangePasswordController|edit: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }
    /**
     * Laravel Passport User Change Password  API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'pin' => 'required|integer|max:100000|min:10',
                'current_password' => 'required|min:8|max:255',
                'password' => 'required|string|min:8|max:255|confirmed|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/',
                'password_confirmation' => 'required|string|min:8|max:255|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,255}$/',
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    // 'message' => $credentials->messages()->all(),
                ], 400);
            }

            // Compare old password and new password for match.
            if ($request->current_password === $request->password) {

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
                ], 401);
            }

            // Delete token via id.
            DB::table('password_resets')->delete($dbToken->id);

            // Updated user password.
            $user = User::where('email', $request->user()->email);

            $user->update([
                'password' => Hash::make($request->password),
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
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangePasswordController|update: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangePasswordController|update: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }
}
