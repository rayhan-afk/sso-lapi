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
    public function redirect()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    public function callback()
    {
        try {
            // Kita paksa Socialite menggunakan host.docker.internal khusus untuk proses ini
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

            // --- 1. PROSES PENYIMPANAN KE DATABASE (HYBRID SSO) ---
            $user = User::updateOrCreate(
                ['email' => $keycloakUser->getEmail()],
                [
                    'id'               => $keycloakUser->getId(),
                    'nama'             => $keycloakUser->getName(),
                    'password'         => bcrypt(Str::random(16)),
                    'is_active'        => true,
                    // TAMBAHAN: Simpan access_token ke database
                    'sso_access_token' => $keycloakUser->token 
                ]
            );

            Auth::login($user);

            $appSso = Application::where('client_id', 'sso-lapi')->first();
            $appId = $appSso ? $appSso->id : 1; // Fallback ke 1 jika data belum ada di database

            ActivityLogger::log(
                'LOGIN_SSO', 
                'User berhasil login ke Portal SSO', 
                $user->id, 
                $appId // Menggunakan ID yang dinamis
            );

            // --- 2. PROSES PENYIMPANAN KE SESSION ---
            // Simpan access_token ke session untuk verifikasi Middleware (Satpam)
            session(['sso_hybrid_token' => $keycloakUser->token]);

            // Ambil id_token (biasanya ada di response body OIDC) untuk keperluan Logout Global
            $idToken = $keycloakUser->accessTokenResponseBody['id_token'] ?? null;
            if ($idToken) {
                session(['sso_id_token' => $idToken]);
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'SSO Error: ' . $e->getMessage(),
                'hint' => 'Pastikan Docker container Laravel bisa melakukan ping ke host.docker.internal'
            ], 500);
        }
    }

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