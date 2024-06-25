<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['throttle:3,1'])->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/email/verify/{url}', [AuthController::class, 'verifyEmail'])
        ->name('verify_email');

    Route::post('/forget/password', [PasswordController::class, 'forget']);

    Route::post('/reset/password/{url}', [PasswordController::class, 'reset'])
        ->name('reset_password');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/*', function () {
        return response()->json([
            'status' => false,
            'message' => __('auth.unauthenticated'),
        ], 403);
    })->name('login');
});

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

    Route::post('/edit/password', [PasswordController::class, 'edit']);

    Route::post('/update/password', [PasswordController::class, 'update']);

    Route::get('/dashboard', function () {
        return 'Projects Dashboard Successfully!';
    });

    Route::get('/account', function () {
        return 'Projects Account Successfully!';
    });
});


// -> https://xfinity-software/demo_shop


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
| Test Mail Routes
|--------------------------------------------------------------------------
*/

Route::get('test/mails', function () {
    \Illuminate\Support\Facades\Mail::to('test@test.de')->send(new App\Mail\Auth\RegisterSuccessMail(\App\Traits\Favicon\Base64Trait::getEmailLogo()));
});
