<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
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
        // Pastikan kodenya seperti ini:
        $keycloakUser = Socialite::driver('keycloak')->stateless()->user();
        
        $user = User::updateOrCreate(
            ['email' => $keycloakUser->getEmail()],
            [
                'id' => $keycloakUser->getId(),
                'nama' => $keycloakUser->getName(),
                'password' => bcrypt(Str::random(16)),
                'is_active' => true
            ]
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }

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