<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application; 
use App\Services\ActivityLogger;

class LogController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data yang lebih sedikit
        $request->validate([
            'email'      => 'required|email',
            'app_id'     => 'required|integer',
            'event_code' => 'required|string', // Menerima kode event (misal: 'LOGIN')
            'ip_address' => 'nullable|string',
            'user_agent' => 'nullable|string',
        ]);

        // 2. Cari Data User
        $user = User::where('email', $request->email)->first();
        $userId = $user ? $user->id : null;

        // 3. Cari Data Aplikasi untuk mengetahui namanya (ITBLab / Sista / dll)
        $app = Application::find($request->app_id);
        $appName = $app ? $app->app_name : 'Aplikasi Tidak Dikenal';

        // --- 4. LOGIKA PENERJEMAH EVENT KODE (OTAKNYA DI SINI) ---
        $actionType = 'SYSTEM_EVENT';
        $description = 'Aktivitas sistem tidak dikenal';

        // Kita gunakan Switch-Case untuk mengecek kode dari ITBLab/Sista
        $actionType = 'SYSTEM_EVENT';
        $description = 'Aktivitas sistem tidak dikenal';

        // Kita gunakan Switch-Case untuk mengecek kode dari ITBLab/Sista
        switch (strtoupper($request->event_code)) {
            case 'LOGIN':
                // Ganti spasi dengan garis bawah (_) khusus untuk actionType
                $actionType = 'LOGIN_' . str_replace(' ', '_', strtoupper($appName)); 
                $description = "User berhasil login ke {$appName} melalui SSO";
                break;
            
            case 'LOGOUT':
                $actionType = 'LOGOUT_' . str_replace(' ', '_', strtoupper($appName));
                $description = "User logout dari {$appName}";
                break;
            
            case 'UPDATE_PROFILE':
                $actionType = 'UPDATE_' . str_replace(' ', '_', strtoupper($appName));
                $description = "User memperbarui profil di {$appName}";
                break;

            // Tambahkan kode lain di masa depan jika perlu...
        }
        // ---------------------------------------------------------

        // 5. Catat ke database menggunakan ActivityLogger
        ActivityLogger::log(
            $actionType,
            $description,
            $userId,
            $request->app_id,
            $request->ip_address,
            $request->user_agent
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Log aktivitas berhasil dicatat'
        ], 200);
    }
}