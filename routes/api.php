<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangeEmailController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\PinController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route::get('privacy_policy', fn() => view('welcome'))->name('privacy_policy');

// Route::get('imprint', fn() => view('welcome'))->name('imprint');

Route::get('/{page}', [PageController::class])
    ->name('page')
    ->where('pages', 'privacy_policy|imprint');

/*
|--------------------------------------------------------------------------
| API Throttle Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['throttle:9,1'])->group(function () {
    
    // Login Route
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['throttle:3,1'])->group(function () {
    
    // Look Up & Register methods
    Route::post('/lookup_account', [AuthController::class, 'lookup']);

    Route::post('/register', [RegisterController::class, 'register']);

    // verify token methods
    Route::get('/email/verify/{url}', [PinController::class, 'index']);

    Route::post('/update/verify/token', [PinController::class, 'store']);

    Route::post('/email/verify/{url}', [RegisterController::class, 'verifyEmail'])
        ->name('verify_email');

    // Forget password methods
    Route::post('/forget/password', [ForgetPasswordController::class, 'edit']);

    Route::put('/reset/password/{url}', [ForgetPasswordController::class, 'update'])
        ->name('reset_password');

    // Unauthenticated method
    Route::get('/*', [AuthController::class, 'unauthenticated'])->name('unauthenticated');
});

/*
|--------------------------------------------------------------------------
| API Authentificate, Throttle & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api', 'verified', 'throttle:6,1'])->group(function () {

    // Change Personal Data Routes
    Route::put('/update/user/personal/data', [UserController::class, 'update']);

    // Change Password
    Route::post('/edit/password', [ChangePasswordController::class, 'edit']);

    Route::put('/update/password', [ChangePasswordController::class, 'update']);

    // Change Email
    Route::post('/edit/email', [ChangeEmailController::class, 'edit']);

    Route::put('/update/email', [ChangeEmailController::class, 'update']);
});


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

    Route::get('/logout', [AuthController::class, 'logout']);

    // User Account actions
    Route::controller(UserController::class)->group(function () {

        Route::get('/account/profile', 'profile');

        Route::get('/account/address', 'address');

        Route::get('/account/orders', 'orders');
    });
});




// -> https://xfinity-software/demo_shop


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

// Route::get('test/mails', function () {
//     \Illuminate\Support\Facades\Mail::to('test@test.de')->send(new App\Mail\Auth\RegisterSuccessMail('https://test-url-test', 168752));
// });
