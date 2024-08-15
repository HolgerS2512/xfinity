<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AesCryptographer;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

final class UserController extends Controller
{
    /**
     * Laravel get the authentification user values API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        try {
            $user = Auth::user();

            $newUser = [
                'salutation' => $user->salutation,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'birthday' => $user->birthday,
            ];

            $AC = new AesCryptographer(config('app.encryption_password'));

            $encrypted = $AC->encrypt(json_encode($newUser));

            return $encrypted;
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
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
        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'salutation' => 'required|string|regex:/^[mdwz]$/',
                'firstname' => 'required|string|max:60|min:2',
                'lastname' => 'required|string|max:40|min:2',
                'birthday' => 'required|date_format:Y-m-d',
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->messages()->all(),
                ], 400);
            }

            // Updated user data.
            $userId = Auth::id();
            $user = User::findOrFail($userId);

            $user->update([
                'salutation' => $request->salutation,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'birthday' => $request->birthday,
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => __('messages.data_updated'),
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Laravel get the authentification user address values API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function addresses()
    {
        try {
            $user = Auth::user();
            $user = User::findOrFail($user->id);

            $addresses = $user->addresses()->get();

            $AC = new AesCryptographer(config('app.encryption_password'));

            $encrypted = $AC->encrypt(json_encode($addresses->toArray()));

            return $encrypted;
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}

// $user = User::find(1);

// $user->addresses()->create([
//     'address_type' => 'billing',
//     'street' => 'Neue Straße',
//     'house_number' => '123',
//     'city' => 'Berlin',
//     'zip' => '10115',
//     'country' => 'DE',
//     'active' => true,
// ]);

