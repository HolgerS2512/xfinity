<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PinRequest;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\Api\GetApiCodesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\Auth\VerifyEmailToken;

class PinController extends Controller
{
    /**
     * Laravel new verify token API Function
     * 
     * @param \App\Http\Requests\Auth\PinRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(PinRequest $request)
    {
        try {
            // Check if user doesnt exist.
            if (User::where('email', $request->email)->doesntExist()) {

                return response()->json([
                    'status' => false,
                    'message' => __('auth.email_exists'),
                ], 401);
            }

            // Find User via email.
            $user = User::where('email', $request->email)->first();

            if ($user) {

                // Create auth token and url.
                $token = GetApiCodesTrait::create()->token;
                $urlCode = GetApiCodesTrait::create()->url;

                // Update or create verify_email_token
                VerifyEmailToken::updateOrCreate([
                    'user_id'   => $user->id
                ], [
                    'token' => $token,
                    'url' => $urlCode,
                    'updated_at' => Carbon::now(),
                ]);

                // Send mail with values.
                Mail::to($user->email)->send(new VerifyEmailMail(route('verify_email', [$urlCode]), $token));

                return response()->json([
                    'status' => true,
                ], 200);
            }

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
