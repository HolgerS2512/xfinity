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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                //
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->messages()->all(),
                ], 400);
            }

            // Is current user logged in and this request id
            $authId = Auth::id();

            if ($authId !== $id) {
                DB::rollBack();
                Log::channel('database')
                    ->error('WARNING HACKER!!! Send id: ' . $id . ' auth id: ' . $authId);

                return response()->json([
                    'status' => false,
                    'message' => __('auth.failed'),
                ], 401);
            }

            // Updated user data.
            $user = User::findOrFail($id);

            $user->update([
                //
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
            Log::channel('database')->error('SettingController|update: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
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
    public function updateSubscriber(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validation.
            $credentials = Validator::make($request->all(), [
                'newsletter_subscriber' => 'required|boolean',
            ]);

            if ($credentials->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $credentials->messages()->all(),
                ], 400);
            }

            // Is current user logged in and this request id
            $authId = Auth::id();

            if ($authId !== $id) {
                DB::rollBack();
                Log::channel('database')
                    ->error('WARNING HACKER!!! Send id: ' . $id . ' auth id: ' . $authId);

                return response()->json([
                    'status' => false,
                    'message' => __('auth.failed'),
                ], 401);
            }

            // Updated user data.
            $user = User::findOrFail($id);

            $user->update([
                'newsletter_subscriber' => $request->newsletter_subscriber,
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
            Log::channel('database')->error('SettingController|updateSubscriber: ' . $e->getMessage(), ['exception' => $e]);

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
            // Is current user logged in and this request id
            $authId = Auth::id();

            if ($authId !== $id) {
                DB::rollBack();
                Log::channel('database')
                    ->error('WARNING HACKER!!! Send id: ' . $id . ' auth id: ' . $authId);

                return response()->json([
                    'status' => false,
                    'message' => __('auth.failed'),
                ], 401);
            }

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
            Log::channel('database')->error('SettingController|destroy: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}
