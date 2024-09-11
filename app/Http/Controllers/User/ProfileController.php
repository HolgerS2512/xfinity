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
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        } catch (HttpException $e) {
            Log::channel('database')->error('ProfileController|index: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            Log::channel('database')->error('ProfileController|index: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
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
                    // 'message' => $credentials->messages()->all(),
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
                ], 403);
            }

            // Updated user data.
            $user = User::findOrFail($id);

            $user->update([
                'salutation' => $request->salutation,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'birthday' => $request->birthday,
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
            Log::channel('database')->error('ProfileController|update: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => false,
            ], $e->getStatusCode() ?? 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('database')->error('ProfileController|update: ' . $e->getMessage(), ['exception' => $e]);

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
        //
    }
}
