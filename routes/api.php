<?php

use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangeEmailController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\PinController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Cookie\CookieController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\PaymentMethodsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API App Routes
|--------------------------------------------------------------------------
*/

Route::get('/all/categories', [CategoryController::class, 'allActive'])
    ->name('all_active_categories');

Route::get('/all/products', [ProductController::class, 'allActive'])
    ->name('all_active_products');


// Route::get('imprint', fn() => view('welcome'))->name('imprint');

// Route::get('/{page}', [PageController::class])
//     ->name('page')
//     ->where('pages', 'privacy_policy|imprint');

/*
|--------------------------------------------------------------------------
| API Throttle Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['throttle:9,1'])->group(function () {

    // Login Route
    Route::post('/login', [AuthController::class, 'login']);

    // Cookie handling
    Route::apiResource('/settings/cookie', CookieController::class);
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

    // Contact Route
    Route::post('contact', [ContactController::class, 'create']);

    // Unauthenticated method
    Route::get('/*', [AuthController::class, 'unauthenticated'])->name('unauthenticated');
});

/*
|--------------------------------------------------------------------------
| API Authentificate, Throttle & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified', 'throttle:3,1'])->group(function () {

    // Change Password
    Route::post('/edit/password', [ChangePasswordController::class, 'edit']);

    Route::put('/update/password', [ChangePasswordController::class, 'update']);

    // Change Email
    Route::post('/edit/email', [ChangeEmailController::class, 'edit']);

    Route::put('/update/email', [ChangeEmailController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| API Authentificate & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/account/profile', ProfileController::class);

    Route::apiResource('/account/orders', OrderController::class);

    Route::apiResource('/account/addresses', AddressController::class);

    Route::apiResource('/account/payment', PaymentMethodsController::class);

    Route::apiResource('/account/settings', SettingController::class);

    Route::apiResource('/account/wishlist', WishlistController::class);
});

/*
|--------------------------------------------------------------------------
| API Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::apiResource('category', CategoryController::class);

    Route::apiResource('product', ProductController::class);
});

// --- apiRessource ---
// GET        /admin/category            – index (zeigt eine Liste aller Kategorien)
// POST       /admin/category            – store (erstellt eine neue Kategorie)
// GET/HEAD   /admin/category/{category} – show (zeigt eine einzelne Kategorie)
// PUT/PATCH  /admin/category/{category} – update (aktualisiert eine vorhandene Kategorie)
// DELETE     /admin/category/{category} – destroy (löscht eine Kategorie)


// -> https://xfinity-software/demo_shop





/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
*/

// Route::get('test/mails', function () {
//     \Illuminate\Support\Facades\Mail::to('test@test.de')->send(new App\Mail\Auth\RegisterSuccessMail('https://test-url-test', 168752));
// });
