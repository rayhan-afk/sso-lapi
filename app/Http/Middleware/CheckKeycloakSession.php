<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckKeycloakSession
{
    public function handle(Request $request, Closure $next)
    {
        // Abaikan pengecekan jika belum login atau sedang di halaman login/logout
        if (!Auth::check() || $request->is('auth/*') || $request->is('logout') || $request->is('cek-sesi')) {
            return $next($request);
        }

        $sessionId = session('keycloak_session_id');
        
        // Jika tidak ada session ID yang tersimpan, biarkan masuk (mungkin login cara lama)
        if (!$sessionId) {
            return $next($request);
        }

        try {
            $token = $this->getAdminToken();
            $baseUrl = env('KEYCLOAK_BASE_URL');
            $realm = env('KEYCLOAK_REALM');
            $email = Auth::user()->email;

            // 1. Cari User ID di Keycloak berdasarkan Email
            $userResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/users", [
                'email' => $email,
                'exact' => true
            ]);

            // Jika API gagal atau user tidak ditemukan, biarkan lewat (jangan asal tendang)
            if (!$userResponse->successful() || empty($userResponse->json())) {
                return $next($request); 
            }
            
            $keycloakUserId = $userResponse->json()[0]['id'];

            // 2. Minta daftar sesi aktif milik user ini ke Keycloak
            $sessionsResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/users/$keycloakUserId/sessions");
            
            if ($sessionsResponse->successful()) {
                $activeSessions = $sessionsResponse->json();
                $sessionAlive = false;

                // 3. Cocokkan ID Sesi Laravel dengan daftar sesi di Keycloak
                foreach ($activeSessions as $session) {
                    if ($session['id'] === $sessionId) {
                        $sessionAlive = true;
                        break;
                    }
                }

                // 4. Jika ID Sesi TIDAK ADA di Keycloak, berarti admin sudah mengeklik "Kick"
                if (!$sessionAlive) {
                    Auth::logout();
                    session()->forget('keycloak_session_id');
                    return redirect('/')->with('error', 'Sesi Anda telah diakhiri oleh Admin secara paksa.');
                }
            }

        } catch (\Exception $e) {
            // Jika Keycloak mati/down, jangan ganggu user yang sedang kerja. Cukup catat di log.
            Log::error('Gagal mengecek sesi Keycloak: ' . $e->getMessage());
        }

        return $next($request);
    }

    private function getAdminToken()
    {
        $response = Http::asForm()->post(env('KEYCLOAK_BASE_URL')."/realms/".env('KEYCLOAK_REALM')."/protocol/openid-connect/token", [
            'grant_type'    => 'client_credentials',
            'client_id'     => env('KEYCLOAK_ADMIN_CLIENT_ID'),
            'client_secret' => env('KEYCLOAK_ADMIN_CLIENT_SECRET'),
        ]);

        return $response->json('access_token');
    }
}