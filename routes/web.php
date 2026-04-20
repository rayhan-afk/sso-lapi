<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Pastikan namespace ini sesuai dengan lokasi asli file Controller Anda di folder App/Http/Controllers/
use App\Http\Controllers\UserController; 
use App\Http\Controllers\Admin\ApplicationController; 
use App\Http\Controllers\LogController;
use App\Http\Controllers\LogActivityController;

/*
|--------------------------------------------------------------------------
| Entry Point & Redirects
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Jika user sudah login, langsung arahkan ke dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    
    // Jika belum login, langsung lempar otomatis ke Keycloak
    return redirect()->route('auth.redirect');
})->name('login'); // Nama 'login' WAJIB dipertahankan untuk middleware 'auth'


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
| Route Debug (Hanya Aktif di Lokal)
|--------------------------------------------------------------------------
*/

if (app()->isLocal()) {
    Route::get('/cek-sesi', function () {
        if (Auth::check()) {
            return "BERHASIL! Anda sedang login sebagai: " . Auth::user()->email;
        }
        return "GAGAL! Session kosong.";
    });
}


/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Rute Dashboard Utama (App Launcher)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute pengecekan berkala untuk fitur Heartbeat/Polling
    Route::get('/ping-session-status', function () {
        return response()->json(['status' => 'active']);
    })->name('ping.session');


    /*
    |--------------------------------------------------------------------------
    | Admin Panel
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('admin')->group(function () {

        // --- Manajemen Pengguna ---
        // Rute Spesifik di ATAS Resource agar tidak tertimpa oleh {user} parameter
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Monitoring & Kick Sesi (Back-Channel Logout)
        Route::get('/users/sessions/monitoring', [UserController::class, 'activeSessions'])->name('users.sessions');
        Route::delete('/users/sessions/{session_id}/kick', [UserController::class, 'forceLogout'])->name('users.force-logout');
        
        Route::resource('users', UserController::class);

        // --- Manajemen Aplikasi Klien ---
        Route::resource('applications', ApplicationController::class);

        // --- Logs & Audit Trail ---
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
        
        // --- Activity Log (Monitoring Log) ---
        Route::get('/log-activity', [LogActivityController::class, 'index'])->name('log.activity');

    });
});