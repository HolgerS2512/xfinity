<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Jobs\Auth\SendForgetPasswordEmail;
use App\Mail\Auth\ForgetPasswordMail;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Traits\Api\GetApiCodesTrait;

final class ForgetPasswordController extends Controller
{
    /**
     * Laravel Passport User Forget Password  API Function
     * 
     * @param \App\Http\Requests\Auth\ForgetRequest $request
     * @return \Illuminate\Http\Response
     */
    public function edit(ForgetRequest $request)
    {
        DB::beginTransaction();

        try {
            // Check if user exist.
            if (User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'email_doesnt_exists'],
                ], 400);
            }

            // Generate token and url.
            $token = GetApiCodesTrait::create()->token;
            $urlCode = GetApiCodesTrait::create()->url;

            // Send email.
            // Mail::to($request->email)->send(new ForgetPasswordMail(route('reset_password', [$urlCode]), $token));
            dispatch(new SendForgetPasswordEmail($request->email, route('reset_password', [$urlCode]), $token));

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

            DB::commit();

            return response()->json([
                'status' => true,
                'url' => $urlCode,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

    /**
     * Laravel Passport User Reset Password  API Function
     * 
     * @param \App\Http\Requests\Auth\ResetPasswordRequest $request
     * @param string $url
     * @return \Illuminate\Http\Response
     */
    public function update(ResetPasswordRequest $request, $url)
    {
        DB::beginTransaction();

        try {
            // Find column in table "password_resets".
            $dbToken = DB::table('password_resets')->where('url', $url)->first();

            // Checks url if exist in table.
            if (is_null($dbToken)) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'url_link_not_match'],
                ], 400);
            }

            // Checks for token doesnt match.
            if ($dbToken->token !== (int) $request->pin) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'token_not_match'],
                ], 400);
            }

            // Check if user exist.
            if (User::where('email', $dbToken->email)->doesntExist() || $dbToken->email !== $request->email) {

                return response()->json([
                    'status' => false,
                    'message' => [true, 'email_doesnt_exists'],
                ], 400);
            }

            // Check that the token column “created_at” is not older than 15 minutes.
            if (Carbon::now()->diffInMinutes($dbToken->created_at) > 15) {
                DB::table('password_resets')->delete($dbToken->id);
                DB::commit();

                return response()->json([
                    'status' => false,
                    'message' => [true, 'token_timeout'],
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

            // Delete all expired at tokens
            DB::table('password_resets')->where('created_at', '<', Carbon::now()->subDays(1))->delete();

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
