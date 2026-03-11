<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman awal (sementara kita buat tombol simple)
Route::get('/', function () {
    return '<div style="text-align:center; margin-top:50px;">
                <h2>SSO Portal</h2>
                <a href="/auth/redirect" style="padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; border-radius:5px;">Login via Keycloak</a>
            </div>';
})->name('login');

// Rute Otentikasi
Route::get('/auth/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Dashboard (Mengarah ke View)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');