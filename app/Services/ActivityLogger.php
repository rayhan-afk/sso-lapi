<?php

namespace App\Services;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http; 
use Jenssegers\Agent\Agent;

class ActivityLogger
{
    // 1. Tambahkan parameter $ipAddress dan $userAgent di bagian paling kanan
    public static function log($actionType, $description, $userId = null, $appId = 1, $ipAddress = null, $userAgent = null)
    {
        $agent = new Agent();

        // 2. Jika ada User-Agent titipan dari aplikasi luar (ITBLab), set ke Agent
        if ($userAgent) {
            $agent->setUserAgent($userAgent);
        }

        // Ambil nama platform & browser (tambahkan fallback 'Unknown' jika kosong)
        $platform = $agent->platform() ?: 'Unknown';
        $browser = $agent->browser() ?: 'Unknown';
        
        // 3. Jika platform dan browser 'Unknown - Unknown', kita tangkap nama aplikasinya (misal: Guzzle)
        if ($platform === 'Unknown' && $browser === 'Unknown' && $userAgent) {
            $deviceString = 'Server/API (' . Str::limit($userAgent, 20) . ')';
        } else {
            $deviceString = $platform . ' - ' . $browser;
        }
        
        // 4. Gunakan IP titipan, atau IP asli jika login dari dalam Portal SSO (null)
        $ip = $ipAddress ?? Request::ip();
        $location = 'Unknown';

        // Pengecekan IP lokal/Docker agar tidak error saat hit API (menggunakan variabel $ip yang baru)
        $isLocalIp = $ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '172.') || str_starts_with($ip, '192.168.');

        if (!$isLocalIp) {
            try {
                // Mengambil data lokasi berdasarkan IP menggunakan API gratis
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                
                if ($response->successful() && $response->json('status') === 'success') {
                    // Akan menghasilkan contoh: "Bandung, Indonesia"
                    $location = $response->json('city') . ', ' . $response->json('country');
                }
            } catch (\Exception $e) {
                // Abaikan jika API sedang down/timeout
                $location = 'Unknown';
            }
        } else {
            $location = 'Local Network'; // Fallback jika dijalankan di localhost
        }

        LogActivity::create([
            'user_id'     => $userId ?? auth()->id(),
            'app_id'      => $appId,
            'action_type' => $actionType,
            'description' => $description,
            'ip_address'  => $ip, // Sekarang menyimpan IP user yang valid
            'device'      => $deviceString, // Sekarang menyimpan perangkat user yang valid
            'location'    => $location, 
        ]);
    }
}