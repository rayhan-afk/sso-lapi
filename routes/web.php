<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;


/*
|--------------------------------------------------------------------------
| Login Portal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return '<div style="text-align:center; margin-top:50px;">
                <h2>SSO Portal</h2>
                <a href="/auth/redirect" style="padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; border-radius:5px;">Login via Keycloak</a>
            </div>';
})->name('login');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/auth/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {

    /*
    | Applications
    */

    Route::get('/applications', [ApplicationController::class, 'index'])
        ->name('applications.index');

    Route::get('/applications/create', [ApplicationController::class, 'create'])
        ->name('applications.create');

    Route::post('/applications', [ApplicationController::class, 'store'])
        ->name('applications.store');


    /*
    | Users
    */

/*
| Users
*/

Route::get('/users', [UserController::class, 'index'])
    ->name('users.index');

Route::get('/users/create', [UserController::class, 'create'])
    ->name('users.create');

Route::post('/users', [UserController::class, 'store'])
    ->name('users.store');

    /*
    | Logs
    */

    Route::get('/logs', [LogController::class, 'index'])
        ->name('logs.index');

});