<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Redirect ke halaman Login Keycloak
     */
    public function redirect()
    {
        return Socialite::driver('keycloak')->stateless()->redirect();
    }

    /**
     * Menangani data yang dikirim balik oleh Keycloak
     */
    public function callback()
    {
        try {
            // Ambil data user dari Keycloak secara stateless
            $keycloakUser = Socialite::driver('keycloak')->stateless()->user();
            
            // Cari user di database lokal berdasarkan email
            $user = User::where('email', $keycloakUser->getEmail())->first();

            if (!$user) {
                // Buat user baru jika belum ada
                $user = User::create([
                    'id'       => (string) Str::uuid(), // Memastikan ID menggunakan UUID sesuai UserController kamu
                    'email'    => $keycloakUser->getEmail(),
                    'nama'     => $keycloakUser->getName(),
                    'password' => bcrypt(Str::random(16)), 
                    'is_active'=> true,
                    'role'     => 'user'
                ]);
            } else {
                // Update nama jika sudah ada
                $user->update([
                    'nama' => $keycloakUser->getName(),
                ]);
            }

            // Cek apakah akun dinonaktifkan oleh admin
            if ($user->is_active == 0) {
                $keycloakBaseUrl = env('KEYCLOAK_BASE_URL');
                $realm = env('KEYCLOAK_REALM');
                $clientId = env('KEYCLOAK_CLIENT_ID');
                $redirectUri = urlencode(url('/?error=Akun_Dinonaktifkan_Oleh_Admin'));
                
                return redirect("{$keycloakBaseUrl}/realms/{$realm}/protocol/openid-connect/logout?client_id={$clientId}&post_logout_redirect_uri={$redirectUri}");
            }

            // LOGIN-KAN USER KE SESSION LARAVEL
            Auth::login($user);

            // KUNCI PERBAIKAN: Ambil ID Sesi Keycloak (session_state) dari dua jalur
            // 1. Dari data raw Socialite
            // 2. Dari parameter URL (fallback)
            $sid = $keycloakUser->getRaw()['session_state'] ?? request('session_state');

            if ($sid) {
                session(['keycloak_session_id' => $sid]);
            }

            // PAKSA SIMPAN SESSION
            session()->save();

            // Redirect ke halaman dashboard
            return redirect()->route('dashboard');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            dd("Error Komunikasi Keycloak (ClientException):", $responseBodyAsString);
        } catch (\Exception $e) {
            dd('Error Sistem:', $e->getMessage());
        }
    }

    /**
     * Logout dari Aplikasi dan Keycloak
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $keycloakBaseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $clientId = env('KEYCLOAK_CLIENT_ID');
        $redirectUri = urlencode(url('/'));

        $logoutUrl = "{$keycloakBaseUrl}/realms/{$realm}/protocol/openid-connect/logout?client_id={$clientId}&post_logout_redirect_uri={$redirectUri}";

        return redirect($logoutUrl);
    }
}