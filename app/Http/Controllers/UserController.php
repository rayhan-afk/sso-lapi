<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        // Menunjuk ke folder resources/views/admin/users/index.blade.php
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Menunjuk ke folder resources/views/admin/users/create.blade.php
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'jabatan'  => 'required|string|max:100',
            'role'     => 'required|in:admin,user', // Validasi input role
        ]);

        // Mulai transaksi: Jika gagal di tengah jalan, batalkan semua perubahan di database
        DB::beginTransaction();

        try {
            // 2. Simpan ke Database Lokal dengan UUID
            $user = clone User::create([
                'id'        => Str::uuid()->toString(),
                'nama'      => $request->nama,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'jabatan'   => $request->jabatan,
                'role'      => $request->role, // Simpan role ke database
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // 3. Tembak ke Keycloak
            $this->createUserInKeycloak(
                $request->email,
                $request->nama,
                $request->password,
                $user->is_active
            );

            // 4. Sinkronisasi Role Admin di Keycloak
            $this->syncKeycloakAdminRole($request->email, $request->role);

            // Jika sampai di sini berarti sukses semua, permanenkan data di lokal
            DB::commit();

            return redirect()->route('users.index')
                             ->with('success', 'User berhasil ditambahkan di Dashboard & Keycloak!');

        } catch (\Exception $e) {
            // Jika ada error (misal API Keycloak mati), batalkan penyimpanan lokal
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. Validasi Input
        $request->validate([
            'nama'     => 'required|string|max:150',
            // Email harus unik, tapi abaikan ID user yang sedang di-edit
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'jabatan'  => 'required|string|max:100',
            'role'     => 'required|in:admin,user', // Validasi input role
            'password' => 'nullable|min:8', // Boleh kosong
        ]);

        $isActive = $request->has('is_active') ? 1 : 0;
        $oldEmail = $user->email; // Simpan email lama untuk cari user di Keycloak

        // Mulai Transaksi
        DB::beginTransaction();

        try {
            // 2. Update Database Lokal
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->jabatan = $request->jabatan;
            $user->role = $request->role; // Update role ke database
            $user->is_active = $isActive;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            // 3. Tembak Perubahan ke API Keycloak
            $this->updateUserInKeycloak(
                $oldEmail,
                $request->email,
                $request->nama,
                $request->password, // Akan diproses kalau tidak null
                $isActive
            );

            // 4. Sinkronisasi Role Admin di Keycloak
            $this->syncKeycloakAdminRole($request->email, $request->role);

            // Jika sukses, permanenkan data lokal
            DB::commit();

            return redirect()->route('users.index')
                             ->with('success', 'Data user berhasil diperbarui di Lokal & Keycloak!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $email = $user->email;

        // Mulai Transaksi
        DB::beginTransaction();

        try {
            // 1. Hapus dari Database Lokal
            $user->delete();

            // 2. Hapus dari Keycloak
            $this->deleteUserInKeycloak($email);

            // Jika sukses
            DB::commit();

            return redirect()->route('users.index')
                             ->with('success', 'User berhasil dihapus permanen dari sistem!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // FUNGSI PRIVATE UNTUK API KEYCLOAK
    // ========================================================================

    /**
     * Meminta Token Admin Keycloak
     */
    private function getKeycloakAdminToken()
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');

        $response = Http::asForm()->post("$baseUrl/realms/$realm/protocol/openid-connect/token", [
            'grant_type'    => 'client_credentials',
            'client_id'     => env('KEYCLOAK_ADMIN_CLIENT_ID'),
            'client_secret' => env('KEYCLOAK_ADMIN_CLIENT_SECRET'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal mendapatkan token admin Keycloak.');
        }

        return $response->json('access_token');
    }

    /**
     * Buat User di Keycloak
     */
    private function createUserInKeycloak($email, $name, $password, $isActive)
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        $userResponse = Http::withToken($token)->post("$baseUrl/admin/realms/$realm/users", [
            'username'      => $email, 
            'email'         => $email,
            'firstName'     => $name,
            'enabled'       => $isActive == 1 ? true : false,
            'emailVerified' => true,
            'credentials'   => [
                [
                    'type'      => 'password',
                    'value'     => $password,
                    'temporary' => false, 
                ]
            ]
        ]);

        if ($userResponse->status() === 409) {
            throw new \Exception('Email tersebut sudah terdaftar di dalam sistem SSO Keycloak.');
        } elseif ($userResponse->status() !== 201) {
            throw new \Exception('Gagal menyimpan ke Keycloak: ' . $userResponse->body());
        }
    }

    /**
     * Update User di Keycloak
     */
    private function updateUserInKeycloak($oldEmail, $newEmail, $name, $password, $isActive)
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        // Cari User ID di Keycloak berdasarkan email lama
        $searchResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/users", [
            'email' => $oldEmail,
            'exact' => true
        ]);

        $users = $searchResponse->json();
        if (empty($users)) {
            return true; 
        }

        $keycloakUserId = $users[0]['id'];

        $updateData = [
            'username'  => $newEmail,
            'email'     => $newEmail,
            'firstName' => $name,
            'enabled'   => $isActive == 1 ? true : false,
        ];

        if (!empty($password)) {
            $updateData['credentials'] = [
                [
                    'type'      => 'password',
                    'value'     => $password,
                    'temporary' => false,
                ]
            ];
        }

        $updateResponse = Http::withToken($token)
            ->put("$baseUrl/admin/realms/$realm/users/$keycloakUserId", $updateData);

        if ($updateResponse->status() !== 204) {
             throw new \Exception('Gagal update data di Keycloak.');
        }
    }

    /**
     * Hapus User di Keycloak
     */
    private function deleteUserInKeycloak($email)
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        $searchResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/users", [
            'email' => $email,
            'exact' => true
        ]);

        $users = $searchResponse->json();
        if (empty($users)) {
            return true; 
        }

        $keycloakUserId = $users[0]['id'];

        $deleteResponse = Http::withToken($token)
            ->delete("$baseUrl/admin/realms/$realm/users/$keycloakUserId");

        if ($deleteResponse->status() !== 204) {
             throw new \Exception('Gagal menghapus user di Keycloak.');
        }
    }

    /**
     * Sinkronisasi Role Admin di Keycloak
     */
    private function syncKeycloakAdminRole($email, $role)
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        // 1. Cari User ID di Keycloak
        $userResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/users", [
            'email' => $email,
            'exact' => true
        ]);
        
        $users = $userResponse->json();
        if (empty($users)) return; 
        $userId = $users[0]['id'];

        // 2. Ambil Data Role 'admin' dari Keycloak
        $roleResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/roles/admin");
        
        if ($roleResponse->status() !== 200) {
            throw new \Exception("Gagal mengambil role Keycloak. Status HTTP: " . $roleResponse->status() . " | Pesan: " . $roleResponse->body());
        }
        
        $roleData = $roleResponse->json();
        $mappingUrl = "$baseUrl/admin/realms/$realm/users/$userId/role-mappings/realm";

        // 3. Pasang atau Cabut Role
        if (strtolower($role) === 'admin') {
            Http::withToken($token)->post($mappingUrl, [[
                'id' => $roleData['id'],
                'name' => $roleData['name']
            ]]);
        } else {
            Http::withToken($token)->delete($mappingUrl, [[
                'id' => $roleData['id'],
                'name' => $roleData['name']
            ]]);
        }
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        $newStatus = $user->is_active == 1 ? 0 : 1;

        DB::beginTransaction();

        try {
            $user->is_active = $newStatus;
            $user->save();

            $this->updateUserStatusInKeycloak($user->email, $newStatus);

            DB::commit();

            $statusText = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Akun {$user->nama} berhasil {$statusText}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi Private Khusus untuk Update Status Keycloak
     */
    private function updateUserStatusInKeycloak($email, $isActive)
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        // Cari User ID di Keycloak berdasarkan email
        $searchResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/users", [
            'email' => $email,
            'exact' => true
        ]);

        $users = $searchResponse->json();
        
        // Jika user tidak ada di Keycloak, kita abaikan saja
        if (empty($users)) return true; 

        $keycloakUserId = $users[0]['id'];

        // Tembak perubahan status ke Keycloak
        $updateResponse = Http::withToken($token)
            ->put("$baseUrl/admin/realms/$realm/users/$keycloakUserId", [
                'enabled' => $isActive == 1 ? true : false,
            ]);

        if ($updateResponse->status() !== 204) {
            throw new \Exception('Server Keycloak menolak perubahan status.');
        }
    }

    /**
     * Tampilkan Halaman Monitoring Sesi (Lengkap dari semua Client)
     */
    public function activeSessions()
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        // 1. Ambil statistik aplikasi mana saja yang aktif
        $statsResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/client-session-stats");
        
        if (!$statsResponse->successful()) {
            return back()->with('error', 'Gagal mengambil statistik sesi dari Keycloak.');
        }

        $clientStats = $statsResponse->json();
        
        // dd($clientStats); <-- SUDAH DIHAPUS

        $allSessions = [];
        $sessionIds = []; 

        foreach ($clientStats as $stat) {
            // 2. Cek jika aplikasi ini memiliki user yang sedang aktif
            if (isset($stat['active']) && $stat['active'] > 0) {
                $clientUuid = $stat['id'];
                
                // 3. Minta detail user session ke Keycloak
                $sessionsResponse = Http::withToken($token)->get("$baseUrl/admin/realms/$realm/clients/$clientUuid/user-sessions", [
                    'first' => 0,
                    'max' => 100
                ]);

                // --- BAGIAN PENTING: MUNCULKAN ERROR ASLI ---
                if (!$sessionsResponse->successful()) {
                    throw new \Exception("Akses Ditolak Keycloak! Status: " . $sessionsResponse->status() . " | Pesan: " . $sessionsResponse->body());
                }
                // --------------------------------------------

                $clientSessions = $sessionsResponse->json();
                foreach ($clientSessions as $session) {
                    if (!in_array($session['id'], $sessionIds)) {
                        $sessionIds[] = $session['id'];
                        
                        $user = User::where('email', $session['username'])->first();
                        $session['nama_user'] = $user ? $user->nama : 'User Keycloak';
                        $session['jabatan'] = $user ? $user->jabatan : '-';
                        
                        $allSessions[] = $session;
                    }
                }
            }
        }

        usort($allSessions, function($a, $b) {
            return $b['start'] <=> $a['start'];
        });

        $sessions = $allSessions;

        return view('admin.users.sessions', compact('sessions'));
    }

    /**
     * Memutus Sesi User Secara Paksa (Force Logout)
     */
        public function forceLogout($sessionId)
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $token = $this->getKeycloakAdminToken();

        // Tembak Keycloak untuk Hapus Sesi SSO
        $response = Http::withToken($token)->delete("$baseUrl/admin/realms/$realm/sessions/$sessionId");

        if ($response->successful()) {
            return back()->with('success', 'Sesi di Keycloak berhasil diputus. User akan ter-logout otomatis saat mereka melakukan aktivitas/refresh di aplikasi.');
        }
        
        return back()->with('error', 'Gagal memutus sesi dari Keycloak: ' . $response->body());
    }
}