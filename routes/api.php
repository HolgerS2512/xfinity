<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('privacy_policy', fn() => view('welcome'))->name('privacy_policy');

/*
|--------------------------------------------------------------------------
| API Throttle Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['throttle:3,1'])->group(function () {

    Route::post('/register', [RegisterController::class, 'register']);

    Route::post('/email/verify/{url}', [RegisterController::class, 'verifyEmail'])
        ->name('verify_email');

    Route::post('/forget/password', [ForgetPasswordController::class, 'edit']);

    Route::post('/reset/password/{url}', [ForgetPasswordController::class, 'update'])
        ->name('reset_password');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/*', [AuthController::class, 'unauthenticated'])->name('login');
});

// Route::controller(RegisterController::class)->group(function () {

//     Route::post('/register', 'register');

//     Route::post('/email/verify/{url}', 'verifyEmail')->name('verify_email');
// });

// Route::prefix('admin')->group(function () {
//     Route::get('/users', function () {
//         // Matches The "/admin/users" URL
//     });
// });

/*
|--------------------------------------------------------------------------
| API Authentificate & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api', 'verified'])->group(function () {

    Route::post('/edit/password', [ChangePasswordController::class, 'edit']);

    Route::post('/update/password', [ChangePasswordController::class, 'update']);

    // User Account actions
    Route::controller(UserController::class)->group(function () {

        Route::get('/account/profile', 'profile');

        Route::get('/account/address', 'address');

        Route::get('/account/orders', 'orders');
    });
});


// -> https://xfinity-software/demo_shop

/*
|--------------------------------------------------------------------------
| API Logout Route
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
});





// use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');



/*
|--------------------------------------------------------------------------
| Test Mail Route
|--------------------------------------------------------------------------
*/

Route::get('test/mails', function () {
    \Illuminate\Support\Facades\Mail::to('test@test.de')->send(new App\Mail\Auth\RegisterSuccessMail('https://test-url-test', 168752));
});
