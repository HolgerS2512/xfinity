<?php

use App\Http\Controllers\CookieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/*', [CookieController::class, 'index']);

// --- Important: don't delete this route !!! ---
// applicationin in email
Route::get('privacy_policy', function() {
    redirect('/privacy_policy');
})->name('privacy_policy');

