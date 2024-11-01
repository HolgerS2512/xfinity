<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreRequest;
use App\Http\Requests\Address\UpdateRequest;
use App\Http\Resources\SortDatesRessource;
use App\Models\Address;
use App\Services\Cryption\CryptionService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class AddressController extends Controller
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

            $addresses = Address::where('user_id', $userId)->get();

            $sortedRecords = new SortDatesRessource($addresses);

            $encrypted = $this->cryptionService->encrypt($sortedRecords);

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
     * @param  \App\Http\Requests\Address\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $id = Auth::id();

            // To many Address guard
            $max = 8;
            $counter = Address::where('user_id', $id)->get();

            if ($counter->count() >= $max) {
                return response()->json([
                    'status' => false,
                ], 400);
            }

            $ifExist = [];

            if ($request->active) {
                $ifExist['active'] = $request->active;

                $oldStandard = Address::where('user_id', $id)->where('active', true)->first();

                if ($oldStandard) {
                    $saveStandard = $oldStandard->update(['active' => false]);

                    if (!$saveStandard) {
                        DB::rollBack();

                        return response()->json([
                            'status' => false,
                        ], 500);
                    }
                }
            }

            if ($request->state) {
                $ifExist['state'] = $request->state;
            }

            if ($request->details) {
                $ifExist['details'] = $request->details;
            }

            if ($request->phone) {
                $ifExist['phone'] = $request->phone;
            }

            $address = new Address(array_merge($ifExist, [
                'user_id' => $id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'street' => $request->street,
                'city' => $request->city,
                'zip' => $request->zip,
                'country' => $request->country,
                // 'address_type' => 'billing',
            ]));

            $saved = $address->save();

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
     * @param  \App\Http\Requests\Address\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();

            $ifExist = [];

            if ($request->active) {
                $ifExist['active'] = $request->active;

                $oldStandard = Address::where('user_id', $userId)->where('active', true)->first();

                if ($oldStandard) {
                    $saveStandard = $oldStandard->update(['active' => false]);

                    if (!$saveStandard) {
                        DB::rollBack();

                        return response()->json([
                            'status' => false,
                        ], 500);
                    }
                }
            }

            if ($request->state) {
                $ifExist['state'] = $request->state;
            }

            if ($request->details) {
                $ifExist['details'] = $request->details;
            }

            if ($request->phone) {
                $ifExist['phone'] = $request->phone;
            }

            $address = Address::findOrFail($id);

            $address->update(array_merge([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'street' => $request->street,
                'city' => $request->city,
                'zip' => $request->zip,
                'country' => $request->country,
            ], $ifExist));

            $updated  = $address->save();

            // Check if update success
            if ($updated) {
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
            $address = Address::findOrFail($id);

            $delete  = $address->delete();

            // Check if delete success
            if ($delete) {
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
}
