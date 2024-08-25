<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Cryption\CryptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

final class ProfileController extends Controller
{
    /**
     * The CryptionService instance.
     *
     * @var CryptionService
     */
    protected $cryptionService;

    /**
     * Constructor to initialize the CryptionService.
     *
     * @param CryptionService $cryptionService
     */
    public function __construct(CryptionService $cryptionService)
    {
        $this->cryptionService = $cryptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            $user = User::findOrFail($userId);

            $encrypted = $this->cryptionService->encrypt($user);

            return $encrypted;
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

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

            $saved = $user->save();

            if ($saved) {
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => __('messages.data_updated'),
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Find user
            $user = User::findOrFail($id);

            // Find & delete address, orders, wishlist ...
            // Can delete this???

            dd($user);

            // delete user
            $saved = $user->delete();

            if ($saved) {
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => __('messages.data_updated'),
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}
