<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSetting\UpdateRequest;
use App\Models\User;
use App\Services\Cryption\CryptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

            if ($authId !== $id) {
                DB::rollBack();
                Log::channel('database')
                    ->error('WARNING HACKER!!! Send id: ' . $id . ' auth id: ' . $authId);

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
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('SettingController|updateSubscriber: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('SettingController|updateSubscriber: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
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

            if ($authId !== (int) $id) {
                DB::rollBack();
                Log::channel('database')
                    ->error('WARNING HACKER!!! Send id: ' . $id . ' auth id: ' . $authId);

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
        } catch (HttpException $e) {
            DB::rollBack();
            Log::channel('database')->error('SettingController|destroy: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('SettingController|destroy: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], 500);
        }
    }
}
