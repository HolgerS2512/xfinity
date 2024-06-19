<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// use App\Http\Controllers\OrderController;
 
// Route::controller(OrderController::class)->group(function () {
//     Route::get('/orders/{id}', 'show');
//     Route::post('/orders', 'store');
// });

Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        // Matches The "/admin/users" URL
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register',[AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    });
});

Route::middleware(['auth:api', 'signed'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/Dashboard', function () {
        return 'Projects Fetch Successfully!';
    });
});


// -> https://xfinity-software/demo_shop


// use Illuminate\Foundation\Auth\EmailVerificationRequest;
 
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
 
//     return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');
