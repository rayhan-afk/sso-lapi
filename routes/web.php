<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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
// UBAH DARI ::get MENJADI ::post
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Dashboard (Mengarah ke View)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');