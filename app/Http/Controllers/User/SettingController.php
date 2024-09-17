<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSetting\UpdateRequest;
use App\Models\User;
use App\Services\Cryption\CryptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
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
            $userAttributes = $user->only(['id', 'newsletter_subscriber']);

            $encrypted = $this->cryptionService->encrypt($userAttributes);

            return response()->json([
                'status' => true,
                'data' => $encrypted,
            ], 200);
        } catch (Exception $e) {
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
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
     * @param  \App\Http\Requests\ProfileSetting\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // Is current user logged in and this request id
            $authId = Auth::id();

            if ($authId !== (int) $id) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                ], 403);
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
                ], 200);
            }

            DB::rollBack();

            return response()->json([
                'status' => false,
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
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

            if ($authId !== (int) $id) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                ], 403);
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
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }
}
