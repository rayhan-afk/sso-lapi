<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogActivityController;


// Halaman awal (Langsung Redirect ke SSO)
Route::get('/', function () {
    // Jika user sudah login, langsung arahkan ke dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    
    // Jika belum login, langsung lempar otomatis ke Keycloak
    return redirect('/auth/redirect');
})->name('login'); // Nama 'login' WAJIB dipertahankan untuk middleware 'auth'

// Rute Otentikasi SSO Keycloak
Route::get('/auth/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Debug
Route::get('/cek-sesi', function () {
    if (Auth::check()) {
        return "BERHASIL! Anda sedang login sebagai: " . Auth::user()->email;
    }
    return "GAGAL! Session kosong.";
});

// Grup Rute yang Wajib Login
Route::middleware('auth')->group(function () {
    
    // Rute Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Khusus Admin
    Route::prefix('admin')->group(function () {
        // Rute Spesifik di ATAS Resource
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/sessions/monitoring', [UserController::class, 'activeSessions'])->name('users.sessions');
        Route::delete('/users/sessions/{session_id}/kick', [UserController::class, 'forceLogout'])->name('users.force-logout');
        
        Route::resource('users', UserController::class);
    });

    // Rute pengecekan berkala (Tanpa bungkus middleware tambahan karena sudah di dalam group 'auth')
    Route::get('/ping-session-status', function () {
        return response()->json(['status' => 'active']);
    });

});
// Rute Dashboard Admin(Mengarah ke View)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/log-activity', [LogActivityController::class, 'index'])->name('log.activity');
