<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\UserPaymentMethod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class PaymentMethodsController extends Controller
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
        DB::beginTransaction();

        try {
            // Logik

            DB::commit();

            return response()->json([
                'status' => true,
            ], 200);
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Logik

            DB::commit();

            return response()->json([
                'status' => true,
            ], 200);
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
            // Find user
            $method = PaymentMethod::findOrFail($id);

            // Find & delete address, orders, wishlist ...
            // Can delete this???
            dd($id, $method);

            // delete user
            $saved = $method->delete();

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

    public function setDefaultPaymentMethod(Request $request)
    {
        $user = Auth::user();

        // Setze alle Zahlungsmethoden auf nicht bevorzugt
        // $user->preferredPaymentMethods()->update(['is_default' => false]);

        // Setze die gewählte Zahlungsmethode auf bevorzugt
        // $paymentMethod = $user->preferredPaymentMethods()
        // ->where('id', $request->payment_method_id)
        // ->first();

        // if ($paymentMethod) {
        //     $paymentMethod->is_default = true;
        //     $paymentMethod->save();
        // }

        // return response()->json(['message' => 'Default payment method updated']);
    }

    public function storePayPalPaymentMethod($user, $payPalToken)
    {
        // UserPaymentMethod::create([
        //     'user_id' => $user->id,
        //     'payment_method_id' => PaymentMethod::where('name', 'PayPal')->first()->id,
        //     'is_default' => true,  // Diese Methode als bevorzugt setzen
        //     'external_reference' => $payPalToken  // Speichern des PayPal Tokens
        // ]);
    }
}
