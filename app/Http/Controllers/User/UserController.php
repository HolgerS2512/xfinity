<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
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
     * The cryptographer instance used for encryption and decryption.
     *
     * @var \App\Services\AesCryptographer
     */
    private $cryptographer;

    /**
     * Create a new instance of the class.
     *
     * Initialize the cryptographer with the encryption password from the application configuration.
     */
    public function __construct()
    {
        $this->cryptographer = new AesCryptographer(config('app.encryption_password'));
    }

    /**
     * Laravel get the authentification user values API Function
     * 
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        try {
            $userId = Auth::id();
            $user = User::findOrFail($userId);

            $encrypted = $this->cryptographer->encrypt(json_encode($user));

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
            $userId = Auth::id();

            $addresses = Address::where('user_id', $userId)->get();

            $encrypted = $this->cryptographer->encrypt(json_encode($addresses));

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
