<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // ========================================================================
    // CRUD UTAMA
    // ========================================================================

    /**
     * Menampilkan daftar user.
     */
    public function index()
    {
        $users = User::with('applications')->orderBy('nama', 'asc')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Form tambah user baru.
     */
    public function create()
    {
        $applications = Application::all();
        return view('admin.users.create', compact('applications'));
    }

    /**
     * Simpan user baru ke Keycloak & DB Lokal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'sometimes|string|max:150',  
            'nama'     => 'sometimes|string|max:150', 
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'jabatan'  => 'sometimes|string|max:100',
            'role'     => 'required|in:admin,user',
            'status'   => 'sometimes|in:active,disabled',
            'is_active'=> 'sometimes|boolean',
            'apps'     => 'nullable|array',
        ]);

        $nama = $request->name ?? $request->nama;
        $isActive = $request->has('status') ? ($request->status === 'active' ? 1 : 0) : ($request->has('is_active') ? 1 : 0);

        DB::beginTransaction();

        try {
            // 1. Buat User di Keycloak & Ambil UUID aslinya
            $keycloakUserId = $this->createUserInKeycloak(
                $request->email,
                $nama,
                $request->password,
                $isActive
            );

            // 2. Simpan ke Database Lokal (GUNAKAN UUID DARI KEYCLOAK!)
            $user = User::create([
                'id'        => $keycloakUserId,
                'nama'      => $nama,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'jabatan'   => $request->jabatan ?? $request->role,
                'role'      => $request->role,
                'is_active' => $isActive,
            ]);

            // 3. Sinkronisasi Role Admin
            $this->syncKeycloakAdminRole($request->email, $request->role);

            // 4. Sinkronisasi Akses Aplikasi (Apps)
            $selectedApps = $request->apps ?? [];
            if (!empty($selectedApps)) {
                $user->applications()->sync($selectedApps);
                $this->syncKeycloakClientRoles($keycloakUserId, $selectedApps);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan di Dashboard & Keycloak!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd("ERROR TAMBAH USER: " . $e->getMessage() . " di baris " . $e->getLine());
        }
    }

    /**
     * Menampilkan detail user.
     */
    public function show($id)
    {
        $user = User::with('applications')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Form edit user.
     */
    public function edit($id)
    {
        $user         = User::with('applications')->findOrFail($id);
        $applications = Application::all();
        return view('admin.users.edit', compact('user', 'applications'));
    }

    /**
     * Update data User di Keycloak & DB Lokal.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. VALIDASI (KITA HAPUS VALIDASI EMAIL KARENA PERMANEN)
        $request->validate([
            'name'     => 'sometimes|string|max:150',
            'nama'     => 'sometimes|string|max:150',
            'jabatan'  => 'sometimes|string|max:100',
            'role'     => 'required|in:admin,user',
            'status'   => 'sometimes|in:active,disabled',
            'password' => 'nullable|min:8',
            'apps'     => 'nullable|array',
        ]);

        $nama     = $request->name ?? $request->nama ?? $user->nama;
        $isActive = $request->has('status')
            ? ($request->status === 'active' ? 1 : 0)
            : ($request->has('is_active') ? 1 : 0);
            
        // 2. GUNAKAN EMAIL DARI DATABASE SAJA AGAR AMAN
        $userEmail = $user->email;

        DB::beginTransaction();

        try {
            $token   = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_SERVER_URL');
            $realm   = env('KEYCLOAK_REALM');

            // 3. Update Profile & Status di Keycloak & Ambil UUID Asli
            $keycloakUserId = $this->updateUserInKeycloak(
                $userEmail, // Wajib pakai email lama untuk pencarian awal
                $userEmail, // Email baru (tetap sama karena permanen)
                $nama,
                $request->password,
                $isActive
            );

            // 4. Update Database Lokal (Email tidak diubah)
            $user->nama      = $nama;
            $user->jabatan   = $request->jabatan ?? $request->role;
            $user->role      = $request->role;
            $user->is_active = $isActive;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // 5. Sinkronisasi Role Realm Admin di Keycloak
            $this->syncKeycloakAdminRole($userEmail, $request->role);

            // 6. Reset lalu Sinkronisasi Client Roles Keycloak
            $this->clearAllKeycloakClientRoles($keycloakUserId);
            $selectedApps = $request->apps ?? [];
            $user->applications()->sync($selectedApps);

            if (!empty($selectedApps)) {
                $this->syncKeycloakClientRoles($keycloakUserId, $selectedApps);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Data user berhasil diperbarui di Lokal & Keycloak!');

        } catch (\Exception $e) {
            DB::rollBack();
            // JIKA MASIH GAGAL DI KEYCLOAK, LAYAR HITAM INI PASTI MUNCUL!
            dd("ERROR UPDATE USER: " . $e->getMessage() . " di baris " . $e->getLine());
        }
    }

    /**
     * Hapus User dari Keycloak & DB Lokal.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        DB::beginTransaction();

        try {
            $token   = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_SERVER_URL');
            $realm   = env('KEYCLOAK_REALM');

            // 1. Hapus di Keycloak
            $deleteResponse = Http::withToken($token)
                ->delete("{$baseUrl}/admin/realms/{$realm}/users/{$id}");

            if ($deleteResponse->failed() && $deleteResponse->status() !== 404) {
                throw new \Exception('Gagal menghapus user di Keycloak: ' . $deleteResponse->body());
            }

            // 2. Hapus Lokal (termasuk relasi pivot apps)
            $user->applications()->detach();
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil dihapus permanen dari sistem!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // TOGGLE STATUS
    // ========================================================================

    /**
     * Aktifkan / Nonaktifkan akun user.
     */
    public function toggleStatus($id)
    {
        $user      = User::findOrFail($id);
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

    // ========================================================================
    // MONITORING SESI
    // ========================================================================

    /**
     * Tampilkan Halaman Monitoring Sesi (Lengkap dari semua Client)
     */
    public function activeSessions()
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        // 1. Ambil statistik client yang aktif
        $statsResponse = Http::withToken($token)
            ->get("{$baseUrl}/admin/realms/{$realm}/client-session-stats");

        if (!$statsResponse->successful()) {
            return back()->with('error', 'Gagal mengambil statistik sesi dari Keycloak.');
        }

        $clientStats = $statsResponse->json();
        $allSessions = [];
        $sessionIds  = [];

        foreach ($clientStats as $stat) {
            if (isset($stat['active']) && $stat['active'] > 0) {
                $clientUuid = $stat['id'];

                $sessionsResponse = Http::withToken($token)
                    ->get("{$baseUrl}/admin/realms/{$realm}/clients/{$clientUuid}/user-sessions", [
                        'first' => 0,
                        'max'   => 100,
                    ]);

                if (!$sessionsResponse->successful()) {
                    throw new \Exception(
                        "Akses Ditolak Keycloak! Status: " . $sessionsResponse->status() .
                        " | Pesan: " . $sessionsResponse->body()
                    );
                }

                foreach ($sessionsResponse->json() as $session) {
                    if (!in_array($session['id'], $sessionIds)) {
                        $sessionIds[] = $session['id'];

                        $dbUser = User::where('email', $session['username'])->first();
                        $session['nama_user'] = $dbUser ? $dbUser->nama    : 'User Keycloak';
                        $session['jabatan']   = $dbUser ? $dbUser->jabatan : '-';

                        $allSessions[] = $session;
                    }
                }
            }
        }

        // Urutkan sesi terbaru di atas
        usort($allSessions, fn($a, $b) => $b['start'] <=> $a['start']);

        $sessions = $allSessions;

        return view('admin.users.sessions', compact('sessions'));
    }

    /**
     * Memutus Sesi User Secara Paksa (Force Logout)
     */
    public function forceLogout($sessionId)
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        $response = Http::withToken($token)
            ->delete("{$baseUrl}/admin/realms/{$realm}/sessions/{$sessionId}");

        if ($response->successful()) {
            return back()->with('success', 'Sesi di Keycloak berhasil diputus. User akan ter-logout otomatis saat mereka melakukan aktivitas/refresh di aplikasi.');
        }

        return back()->with('error', 'Gagal memutus sesi dari Keycloak: ' . $response->body());
    }

    // ========================================================================
    // PRIVATE HELPERS — KEYCLOAK AUTH
    // ========================================================================

    /**
     * Meminta Token Admin Keycloak.
     * Mendukung dua strategi: client_credentials (main) & password grant (branch).
     * Prioritas: client_credentials jika KEYCLOAK_ADMIN_CLIENT_ID tersedia,
     * fallback ke password grant jika menggunakan admin-cli.
     */
    private function getKeycloakAdminToken(): string
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL'); 
        $realm   = env('KEYCLOAK_REALM');

        // Strategi 1: Client Credentials (dipakai oleh controller main)
        if (env('KEYCLOAK_ADMIN_CLIENT_ID') && env('KEYCLOAK_ADMIN_CLIENT_SECRET')) {
            $response = Http::asForm()->post(
                "{$baseUrl}/realms/{$realm}/protocol/openid-connect/token",
                [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => env('KEYCLOAK_ADMIN_CLIENT_ID'),
                    'client_secret' => env('KEYCLOAK_ADMIN_CLIENT_SECRET'),
                ]
            );

            if ($response->successful()) {
                return $response->json('access_token');
            }
        }

        // Strategi 2: Password Grant via admin-cli (dipakai oleh controller branch)
        $response = Http::asForm()->post(
            "{$baseUrl}/realms/master/protocol/openid-connect/token",
            [
                'grant_type' => 'password',
                'client_id'  => 'admin-cli',
                'username'   => env('KEYCLOAK_ADMIN_USER'),
                'password'   => env('KEYCLOAK_ADMIN_PASS'),
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Gagal mendapatkan token admin Keycloak.');
        }

        return $response->json('access_token');
    }

    // ========================================================================
    // PRIVATE HELPERS — KEYCLOAK USER OPERATIONS
    // ========================================================================

    /**
     * Update data user di Keycloak (cari lewat email lama).
     */
    private function updateUserInKeycloak($oldEmail, $newEmail, $name, $password, $isActive): string
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        // 1. CARI UUID KEYCLOAK ASLI BERDASARKAN EMAIL LAMA
        $userResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $oldEmail,
            'exact' => true,
        ]);

        $users = $userResponse->json();
        if (empty($users)) {
            throw new \Exception("User Keycloak dengan email {$oldEmail} tidak ditemukan.");
        }

        $keycloakUserId = $users[0]['id']; // Ini UUID Keycloak Asli!

        // 2. LAKUKAN UPDATE MENGGUNAKAN UUID ASLI TERSEBUT
        $updateData = [
            'username'  => $newEmail,
            'email'     => $newEmail,
            'firstName' => $name,
            'enabled'   => (bool) $isActive,
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
            ->put("{$baseUrl}/admin/realms/{$realm}/users/{$keycloakUserId}", $updateData);

        if ($updateResponse->status() !== 204) {
            throw new \Exception('Gagal update data di Keycloak. Pesan: ' . $updateResponse->body());
        }

        return $keycloakUserId; // Kembalikan ID asli untuk keperluan sync role
    }

    /**
     * Buat data user baru di Keycloak & kembalikan UUID-nya.
     */
    private function createUserInKeycloak($email, $name, $password, $isActive): string
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        // Data User Baru
        $userData = [
            'username'  => $email,
            'email'     => $email,
            'firstName' => $name,
            'enabled'   => (bool) $isActive,
            'emailVerified' => true,
            'credentials' => [
                [
                    'type'      => 'password',
                    'value'     => $password,
                    'temporary' => false,
                ]
            ]
        ];

        // 1. Eksekusi API Buat User
        $createResponse = Http::withToken($token)
            ->post("{$baseUrl}/admin/realms/{$realm}/users", $userData);

        if ($createResponse->status() !== 201 && $createResponse->status() !== 409) {
            throw new \Exception('Gagal membuat user di Keycloak. Error: ' . $createResponse->body());
        }

        // 2. Ambil UUID User yang baru dibuat
        $userResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $email,
            'exact' => true,
        ]);

        $users = $userResponse->json();
        if (empty($users)) {
            throw new \Exception("User berhasil dibuat di Keycloak, tapi gagal mengambil UUID.");
        }

        return $users[0]['id'];
    }

    /**
     * Update hanya status enabled/disabled user di Keycloak.
     */
    private function updateUserStatusInKeycloak($email, $isActive): void
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        $userResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $email,
            'exact' => true,
        ]);

        $users = $userResponse->json();
        if (empty($users)) throw new \Exception("User Keycloak tidak ditemukan saat ubah status.");
        
        $keycloakUserId = $users[0]['id'];

        $updateResponse = Http::withToken($token)
            ->put("{$baseUrl}/admin/realms/{$realm}/users/{$keycloakUserId}", [
                'username' => $email, // FIX: Username wajib dikirim ulang ke Keycloak!
                'enabled'  => (bool) $isActive,
            ]);

        if ($updateResponse->status() !== 204) throw new \Exception('Server Keycloak menolak perubahan status.');
    }

    // ========================================================================
    // PRIVATE HELPERS — KEYCLOAK ROLE OPERATIONS
    // ========================================================================

    /**
     * Sinkronisasi Realm Role 'admin' di Keycloak.
     */
    private function syncKeycloakAdminRole($email, $role): void
    {
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        $userResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $email,
            'exact' => true,
        ]);

        $users = $userResponse->json();
        if (empty($users)) return;
        $userId = $users[0]['id'];

        $roleResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/roles/admin");

        // FIX: Jangan batalkan proses jika role admin belum dibuat di Keycloak
        if ($roleResponse->status() !== 200) {
            \Illuminate\Support\Facades\Log::warning("Role 'admin' belum dibuat di Keycloak.");
            return; 
        }

        $roleData   = $roleResponse->json();
        $mappingUrl = "{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/realm";
        $rolePayload = [['id' => $roleData['id'], 'name' => $roleData['name']]];

        if (strtolower($role) === 'admin') {
            Http::withToken($token)->post($mappingUrl, $rolePayload);
        } else {
            Http::withToken($token)->delete($mappingUrl, $rolePayload);
        }
    }

    /**
     * Sinkronisasi Client Roles per Aplikasi di Keycloak.
     */
    private function syncKeycloakClientRoles($userId, $appIds): void
    {
        $token   = $this->getKeycloakAdminToken();
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');

        $apps = Application::whereIn('id', $appIds)->get();

        foreach ($apps as $app) {
            $clientUuid = $app->keycloak_client_uuid;
            
            // FIX: Lewati aplikasi yang belum memiliki UUID Keycloak (Aman dari error)
            if (empty($clientUuid)) continue; 

            $roleName   = 'access'; 
            $roleResponse = Http::withToken($token)
                ->get("{$baseUrl}/admin/realms/{$realm}/clients/{$clientUuid}/roles/{$roleName}");

            if ($roleResponse->successful()) {
                $roleData = $roleResponse->json();
                Http::withToken($token)->post(
                    "{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/clients/{$clientUuid}",
                    [['id' => $roleData['id'], 'name' => $roleData['name']]]
                );
            }
        }
    }

    /**
     * Hapus semua Client Roles user sebelum update (Reset).
     */
    private function clearAllKeycloakClientRoles($userId): void
    {
        $token   = $this->getKeycloakAdminToken();
        $baseUrl = env('KEYCLOAK_SERVER_URL');
        $realm   = env('KEYCLOAK_REALM');

        $apps = Application::all();

        foreach ($apps as $app) {
            $clientUuid = $app->keycloak_client_uuid;

            // FIX: Lewati aplikasi yang belum memiliki UUID Keycloak
            if (empty($clientUuid)) continue;

            $currentRoles = Http::withToken($token)
                ->get("{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/clients/{$clientUuid}")
                ->json();

            if (!empty($currentRoles) && is_array($currentRoles)) {
                Http::withToken($token)->delete(
                    "{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/clients/{$clientUuid}",
                    $currentRoles
                );
            }
        }
    }
}