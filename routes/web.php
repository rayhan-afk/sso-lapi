<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LogController;

/*
|--------------------------------------------------------------------------
| Entry Point & Redirects
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route login standar yang akan melempar user ke Keycloak
Route::get('/login', function () {
    return redirect()->route('auth.redirect');
})->name('login');


/*
|--------------------------------------------------------------------------
| Authentication (Keycloak OIDC)
|--------------------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    Route::get('/auth/redirect', 'redirect')->name('auth.redirect');
    Route::get('/auth/callback', 'callback')->name('auth.callback');
    Route::post('/logout', 'logout')->name('logout');
});


/*
|--------------------------------------------------------------------------
| Dashboard (SSO Portal)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Admin Panel (Protected by Auth Middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::resource('applications', ApplicationController::class);
    Route::resource('users', UserController::class);

    /**
     * Logs & Monitoring
     */
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

});