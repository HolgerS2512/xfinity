<?php

use App\Http\Controllers\CategoryController as PublicCategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangeEmailController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\PinController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\AddressController as UserAddrController;
use App\Http\Controllers\User\ProfileController;
use App\Models\VersionManager;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/



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

Route::middleware(['auth:api', 'verified', 'throttle:6,1'])->group(function () {

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

Route::middleware(['auth:api', 'verified'])->group(function () {

    Route::get('/logout', [AuthController::class, 'logout']);

    // User Account actions
    // Route::controller(UserController::class)->group(function () {

        // Route::get('/account/profile', 'profile');

        // Route::apiResource('/account/addresses', UserAddrController::class);

        // Route::get('/account/orders', 'orders');
    // });

    Route::apiResource('/account/profile', ProfileController::class);

    Route::apiResource('/account/addresses', UserAddrController::class);

    // Route::get('/account/orders', 'orders');
});

/*
|--------------------------------------------------------------------------
| API App Routes
|--------------------------------------------------------------------------
*/


Route::get('/all/categories', [CategoryController::class, 'allActive'])
    ->name('all_active_categories');


/*
|--------------------------------------------------------------------------
| API Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['role:admin'])->group(function () {

    Route::apiResource('category', CategoryController::class);

});

// --- apiRessource ---
// GET        /admin/category            – index (zeigt eine Liste aller Kategorien)
// POST       /admin/category            – store (erstellt eine neue Kategorie)
// GET        /admin/category/{category} – show (zeigt eine einzelne Kategorie)
// PUT/PATCH  /admin/category/{category} – update (aktualisiert eine vorhandene Kategorie)
// DELETE     /admin/category/{category} – destroy (löscht eine Kategorie)


// -> https://xfinity-software/demo_shop


// use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');



/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
*/

// Route::get('test/mails', function () {
//     \Illuminate\Support\Facades\Mail::to('test@test.de')->send(new App\Mail\Auth\RegisterSuccessMail('https://test-url-test', 168752));
// });

// Route::get('test', function () {
//     \Illuminate\Support\Facades\App::setLocale('en');
//     return __('shop.0');
// });

// Create new db entry in table
// Route::get('/new/version/manager', function() {
//     VersionManager::create([]);
// });