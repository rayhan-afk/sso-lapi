<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil API Key dari Header 'X-API-KEY'
        $apiKey = $request->header('X-API-KEY');
        
        // 2. Ambil kunci rahasia yang terdaftar di .env Portal SSO
        $validKey = env('SSO_API_KEY');

        // 3. Validasi: Jika kosong atau tidak cocok, tolak akses!
        if (empty($apiKey) || $apiKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: API Key tidak valid.'
            ], 401);
        }

        return $next($request);
    }
}