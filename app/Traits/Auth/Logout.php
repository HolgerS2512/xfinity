<?php

namespace App\Traits\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

trait Logout
{
  /**
   * Laravel Passport User Logout  API Function
   * 
   */
  public function logout(Request $request)
  {
    try {
      // $user = $request->user();
      // $user->currentAccessToken->delete();
      // $token->revoke();
      $token = $request->user()->token();

      DB::table('oauth_access_tokens')->delete($token->id);
      DB::table('oauth_access_tokens')->where('expires_at', '<', Carbon::now()->subMonths(6))->delete();

      return response()->json([
        'status' => true,
        'message' => __('auth.logout'),
      ], 204);
    } catch (Exception $e) {

      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}
