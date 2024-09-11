<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\Auth\SendChangeMailEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\Api\GetApiCodesTrait;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ChangeEmailController extends Controller
{
    /**
     * Edit the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'current_email' => 'required|email',
                'email' => 'required|email|confirmed|unique:users,email',
                'email_confirmation' => 'required|email',
            ]);

            if ($credentials->fails()) {
                return response()->json([
                    'status' => false,
                    // 'message' => $credentials->messages()->all(),
                    // 'message' => $credentials->errors(),
                ], 400);
            }

            // Compare old email and new email for match.
            if ($request->email === $request->user()->email) {

                return response()->json([
                    'status' => false,
                    'message' => 'email_match_old_new',
                ], 400);
            }

            // Compares emails for matches.
            if ($request->current_email !== $request->user()->email) {

                return response()->json([
                    'status' => false,
                    'message' => 'email_not_match_db_email',
                ], 400);
            }

            // Generate auth token.
            $token = GetApiCodesTrait::create()->token;

            // Delete all coulmns is older than one hour.
            DB::table('email_resets')->where('created_at', '<', Carbon::now()->subHour(1))->delete();

            // Send email.
            // Mail::to($request->user()->email)->send(new ChangeEmailMail($token));
            dispatch(new SendChangeMailEmail($request->user()->email, $token));

            // Search and check is column exist. Inserted or updated "email_resets" table.
            $resetToken = DB::table('email_resets')->where('email', $request->user()->email)->get();

            $values = [
                'email' => $request->user()->email,
                'token' => $token,
                // 'created_at' => Carbon::now(),
            ];

            if (boolval(count($resetToken))) {
                DB::table('email_resets')->update($values);
            } else {
                DB::table('email_resets')->insert($values);
            }

            DB::commit();

            return response()->json([
                'status' => true,
            ], 200);
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangeEmailController|edit: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangeEmailController|edit: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'pin' => 'required|integer|max:100000|min:10000',
                'current_email' => 'required|email',
                'email' => 'required|email|confirmed|unique:users,email',
                'email_confirmation' => 'required|email',
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    // 'message' => $credentials->messages()->all(),
                ], 400);
            }

            // Compare old email and new email for match.
            if ($request->email === $request->user()->email) {

                return response()->json([
                    'status' => false,
                    'message' => 'email_match_old_new',
                ], 401);
            }

            // Compares emails for matches.
            if ($request->current_email !== $request->user()->email) {

                return response()->json([
                    'status' => false,
                    'message' => 'email_not_match_db_email',
                ], 400);
            }

            // Find column in table "email_resets".
            $dbToken = DB::table('email_resets')->where('email', $request->current_email)->first();

            // Checks for token match.
            if ($dbToken->token !== (int) $request->pin) {

                return response()->json([
                    'status' => false,
                    'message' => 'token_not_match',
                ], 400);
            }

            // Delete token via id.
            DB::table('email_resets')->delete($dbToken->id);

            // Updated user email.
            $user = User::where('email', $request->current_email);

            $user->update([
                'email' => $request->email,
                'updated_at' => Carbon::now(),
            ]);

            // Delete old current user tokens
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
            Log::channel('database')->error('ChangeEmailController|update: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('ChangeEmailController|update: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }
}
