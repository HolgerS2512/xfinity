<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register',[AuthController::class, 'register']);

Route::post('/email/verify/{code}', [AuthController::class, 'verifyEmail'])
    ->middleware(['throttle:3,1'])
    ->name('verify');

// use App\Http\Controllers\OrderController;
 
// Route::controller(OrderController::class)->group(function () {
//     Route::get('/orders/{id}', 'show');
//     Route::post('/orders', 'store');
// });

// Route::prefix('admin')->group(function () {
//     Route::get('/users', function () {
//         // Matches The "/admin/users" URL
//     });
// });

/*
|--------------------------------------------------------------------------
| Authentificate & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/change/password', [PasswordController::class, 'changePassword']);

    Route::post('/new/password', [PasswordController::class, 'newPassword']);

    Route::get('/dashboard', function () {
        return 'Projects Fetch Successfully!';
    });
});


// -> https://xfinity-software/demo_shop


// Route::middleware(['auth:api'])->group(function () {
// });

// use Illuminate\Foundation\Auth\EmailVerificationRequest;
 
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
 
//     return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');
