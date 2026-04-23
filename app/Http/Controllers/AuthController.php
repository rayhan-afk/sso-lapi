<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Application;
use App\Services\ActivityLogger;
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
            // --- 1. AMBIL DATA USER (Dari sso-lapi-docker) ---
            // Menggunakan konfigurasi host.docker.internal khusus untuk proses ini
            $keycloakUser = Socialite::driver('keycloak')
                ->setConfig(new \SocialiteProviders\Manager\Config(
                    env('KEYCLOAK_CLIENT_ID'),
                    env('KEYCLOAK_CLIENT_SECRET'),
                    env('KEYCLOAK_REDIRECT_URI'),
                    [
                        'base_url' => env('KEYCLOAK_SERVER_URL'), 
                        'realms'   => env('KEYCLOAK_REALM'),
                    ]
                ))
                ->stateless()
                ->user();

            // --- 2. PROSES PENYIMPANAN KE DATABASE (Gabungan) ---
            // Cari user di database lokal berdasarkan email
            $user = User::where('email', $keycloakUser->getEmail())->first();

            if (!$user) {
                // Buat user baru menggunakan UUID & simpan access_token
                $user = User::create([
                    'id'               => (string) Str::uuid(), 
                    'email'            => $keycloakUser->getEmail(),
                    'nama'             => $keycloakUser->getName(),
                    'password'         => bcrypt(Str::random(16)), 
                    'is_active'        => true,
                    'role'             => 'user',
                    'sso_access_token' => $keycloakUser->token 
                ]);
            } else {
                // Update nama dan access_token jika user sudah ada
                $user->update([
                    'nama'             => $keycloakUser->getName(),
                    'sso_access_token' => $keycloakUser->token
                ]);
            }

            // --- 3. CEK STATUS AKTIF (Dari main) ---
            // Cek apakah akun dinonaktifkan oleh admin
            if ($user->is_active == 0) {
                $keycloakBaseUrl = env('KEYCLOAK_BASE_URL');
                $realm = env('KEYCLOAK_REALM');
                $clientId = env('KEYCLOAK_CLIENT_ID');
                $redirectUri = urlencode(url('/?error=Akun_Dinonaktifkan_Oleh_Admin'));
                
                return redirect("{$keycloakBaseUrl}/realms/{$realm}/protocol/openid-connect/logout?client_id={$clientId}&post_logout_redirect_uri={$redirectUri}");
            }

            // --- 4. LOGIN & LOGGING (Dari sso-lapi-docker) ---
            Auth::login($user);

            $appSso = Application::where('client_id', 'sso-lapi')->first();
            $appId = $appSso ? $appSso->id : 1; 

            ActivityLogger::log(
                'LOGIN_SSO', 
                'User berhasil login ke Portal SSO', 
                $user->id, 
                $appId
            );

            // --- 5. PROSES PENYIMPANAN KE SESSION (Gabungan) ---
            
            // A. Session State untuk Back-Channel Logout (main)
            $sid = $keycloakUser->getRaw()['session_state'] ?? request('session_state');
            if ($sid) {
                session(['keycloak_session_id' => $sid]);
            }

            // B. Access Token untuk Middleware (sso-lapi-docker)
            session(['sso_hybrid_token' => $keycloakUser->token]);

            // C. ID Token untuk Logout Global (sso-lapi-docker)
            $idToken = $keycloakUser->accessTokenResponseBody['id_token'] ?? null;
            if ($idToken) {
                session(['sso_id_token' => $idToken]);
            }

            // Paksa simpan session sebelum redirect
            session()->save();

            return redirect()->route('dashboard');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Tangkap error spesifik Guzzle dari Keycloak (main)
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            dd("Error Komunikasi Keycloak (ClientException):", $responseBodyAsString);
        } catch (\Exception $e) {
            // Tangkap error umum dengan hint Docker (sso-lapi-docker)
            return response()->json([
                'message' => 'SSO Error: ' . $e->getMessage(),
                'hint' => 'Pastikan Docker container Laravel bisa melakukan ping ke host.docker.internal'
            ], 500);
        }
    }

    /**
     * Logout dari Aplikasi dan Keycloak
     */
    public function logout(Request $request)
    {
        // 1. Ambil id_token dari session SEBELUM session Laravel dihancurkan
        $idToken = session('sso_id_token');

        if (Auth::check()) {
            $appSso = Application::where('client_id', 'sso-lapi')->first();
            $appId = $appSso ? $appSso->id : 1;

            ActivityLogger::log(
                'LOGOUT_SSO', 
                'User logout dari Portal SSO', 
                Auth::id(), 
                $appId
            );
        }

        // 2. Hancurkan sesi lokal di Portal SSO ini
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. Persiapkan URL Logout Keycloak
        $keycloakBaseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $clientId = env('KEYCLOAK_CLIENT_ID');
        $redirectUri = url('/'); // Halaman yang dituju setelah logout sukses

        $logoutUrl = "{$keycloakBaseUrl}/realms/{$realm}/protocol/openid-connect/logout";

        // 4. Susun Parameter
        $params = [
            'client_id' => $clientId,
            'post_logout_redirect_uri' => $redirectUri,
        ];

        // Jika ada id_token, masukkan ke parameter agar Keycloak langsung memproses logout tanpa layar konfirmasi
        if ($idToken) {
            $params['id_token_hint'] = $idToken;
        }

        $logoutUrl .= '?' . http_build_query($params);

        // 5. Redirect ke Keycloak. 
        // Di titik inilah Keycloak akan memanggil Webhook Back-Channel Logout ke Sista dan ITBLab!
        return redirect($logoutUrl);
    }
}