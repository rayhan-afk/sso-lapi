<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SsoWebhookController extends Controller
{
    public function backchannelLogout(Request $request)
    {
        // 1. Tangkap token logout dari Keycloak
        $logoutToken = $request->input('logout_token');

        if (!$logoutToken) {
            return response()->json(['message' => 'Token missing'], 400);
        }

        // 2. Dekode JWT Token
        $parts = explode('.', $logoutToken);
        if (count($parts) !== 3) {
            return response()->json(['message' => 'Invalid token format'], 400);
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        
        // Ambil ID Keycloak (sub)
        $ssoUserId = $payload['sub'] ?? null;

        if ($ssoUserId) {
            // 3. Cari user dan kosongkan tokennya di Database
            // Sesuaikan nama kolom 'id' jika di databasemu menggunakan 'sso_user_id'
            User::where('id', $ssoUserId)->update(['sso_access_token' => null]);
        }

        // 4. Wajib mengembalikan respons 200 OK
        return response('OK', 200);
    }
}