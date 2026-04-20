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
            'name'     => 'required|string|max:150',  // dari branch
            'nama'     => 'sometimes|string|max:150', // fallback dari main
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'jabatan'  => 'sometimes|string|max:100',
            'role'     => 'required|in:admin,user',
            'status'   => 'sometimes|in:active,disabled',
            'is_active'=> 'sometimes|boolean',
            'apps'     => 'nullable|array',
        ]);

        // Normalisasi nama & status agar kompatibel dengan dua format form
        $nama     = $request->name ?? $request->nama;
        $isActive = $request->has('status')
            ? ($request->status === 'active' ? 1 : 0)
            : ($request->has('is_active') ? 1 : 0);

        DB::beginTransaction();

        try {
            $token   = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_BASE_URL');
            $realm   = env('KEYCLOAK_REALM');

            // 1. Buat User di Keycloak
            $payload = [
                'username'      => $request->email,
                'email'         => $request->email,
                'firstName'     => $nama,
                'enabled'       => (bool) $isActive,
                'emailVerified' => true,
                'credentials'   => [
                    [
                        'type'      => 'password',
                        'value'     => $request->password,
                        'temporary' => false,
                    ]
                ],
            ];

            $userResponse = Http::withToken($token)
                ->post("{$baseUrl}/admin/realms/{$realm}/users", $payload);

            if ($userResponse->status() === 409) {
                throw new \Exception('Email tersebut sudah terdaftar di dalam sistem SSO Keycloak.');
            } elseif ($userResponse->failed()) {
                throw new \Exception('Keycloak Store Error: ' . $userResponse->body());
            }

            // 2. Ambil UUID yang digenerate Keycloak
            $search = Http::withToken($token)
                ->get("{$baseUrl}/admin/realms/{$realm}/users", [
                    'email' => $request->email,
                    'exact' => true,
                ])->json();
            $uuid = $search[0]['id'];

            // 3. Simpan ke Database Lokal
            $user = User::create([
                'id'        => $uuid,
                'nama'      => $nama,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'jabatan'   => $request->jabatan ?? $request->role,
                'role'      => $request->role,
                'is_active' => $isActive,
            ]);

            // 4. Sinkronisasi Role Realm Admin di Keycloak (dari main)
            $this->syncKeycloakAdminRole($request->email, $request->role);

            // 5. Sinkronisasi Akses Aplikasi / Client Roles (dari branch)
            if ($request->has('apps')) {
                $user->applications()->sync($request->apps);
                $this->syncKeycloakClientRoles($uuid, $request->apps);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan di Dashboard & Keycloak!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
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

        $request->validate([
            'name'     => 'sometimes|string|max:150',
            'nama'     => 'sometimes|string|max:150',
            'email'    => 'required|email|unique:users,email,' . $user->id,
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
        $oldEmail = $user->email;

        DB::beginTransaction();

        try {
            $token   = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_BASE_URL');
            $realm   = env('KEYCLOAK_REALM');

            // 1. Update Profile & Status di Keycloak
            $updatePayload = [
                'username'  => $request->email,
                'email'     => $request->email,
                'firstName' => $nama,
                'enabled'   => (bool) $isActive,
            ];

            // Jika email berubah, cari user lama dulu lewat email lama
            $this->updateUserInKeycloak(
                $oldEmail,
                $request->email,
                $nama,
                $request->password,
                $isActive
            );

            // 2. Update Database Lokal
            $user->nama      = $nama;
            $user->email     = $request->email;
            $user->jabatan   = $request->jabatan ?? $request->role;
            $user->role      = $request->role;
            $user->is_active = $isActive;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // 3. Sinkronisasi Role Realm Admin di Keycloak (dari main)
            $this->syncKeycloakAdminRole($request->email, $request->role);

            // 4. Reset lalu Sinkronisasi Client Roles Keycloak (dari branch)
            $this->clearAllKeycloakClientRoles($id);
            $selectedApps = $request->apps ?? [];
            $user->applications()->sync($selectedApps);

            if (!empty($selectedApps)) {
                $this->syncKeycloakClientRoles($id, $selectedApps);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Data user berhasil diperbarui di Lokal & Keycloak!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update user: ' . $e->getMessage());
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
            $baseUrl = env('KEYCLOAK_BASE_URL');
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
        $baseUrl = env('KEYCLOAK_BASE_URL');
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
        $baseUrl = env('KEYCLOAK_BASE_URL');
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
        $baseUrl = env('KEYCLOAK_BASE_URL');
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
    private function updateUserInKeycloak($oldEmail, $newEmail, $name, $password, $isActive): void
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        $searchResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $oldEmail,
            'exact' => true,
        ]);

        $users = $searchResponse->json();
        if (empty($users)) return; // User tidak ada di Keycloak, lewati

        $keycloakUserId = $users[0]['id'];

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
            throw new \Exception('Gagal update data di Keycloak.');
        }
    }

    /**
     * Update hanya status enabled/disabled user di Keycloak.
     */
    private function updateUserStatusInKeycloak($email, $isActive): void
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        $searchResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $email,
            'exact' => true,
        ]);

        $users = $searchResponse->json();
        if (empty($users)) return;

        $keycloakUserId = $users[0]['id'];

        $updateResponse = Http::withToken($token)
            ->put("{$baseUrl}/admin/realms/{$realm}/users/{$keycloakUserId}", [
                'enabled' => (bool) $isActive,
            ]);

        if ($updateResponse->status() !== 204) {
            throw new \Exception('Server Keycloak menolak perubahan status.');
        }
    }

    // ========================================================================
    // PRIVATE HELPERS — KEYCLOAK ROLE OPERATIONS
    // ========================================================================

    /**
     * Sinkronisasi Realm Role 'admin' di Keycloak.
     * Pasang role jika user adalah admin, cabut jika bukan.
     */
    private function syncKeycloakAdminRole($email, $role): void
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm   = env('KEYCLOAK_REALM');
        $token   = $this->getKeycloakAdminToken();

        // 1. Cari User ID di Keycloak
        $userResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", [
            'email' => $email,
            'exact' => true,
        ]);

        $users = $userResponse->json();
        if (empty($users)) return;
        $userId = $users[0]['id'];

        // 2. Ambil Data Realm Role 'admin' dari Keycloak
        $roleResponse = Http::withToken($token)
            ->get("{$baseUrl}/admin/realms/{$realm}/roles/admin");

        if ($roleResponse->status() !== 200) {
            throw new \Exception(
                "Gagal mengambil role Keycloak. Status HTTP: " . $roleResponse->status() .
                " | Pesan: " . $roleResponse->body()
            );
        }

        $roleData   = $roleResponse->json();
        $mappingUrl = "{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/realm";
        $rolePayload = [['id' => $roleData['id'], 'name' => $roleData['name']]];

        // 3. Pasang atau Cabut Role
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
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm   = env('KEYCLOAK_REALM');

        $apps = Application::whereIn('id', $appIds)->get();

        foreach ($apps as $app) {
            $clientUuid = $app->client_id_keycloak;
            $roleName   = 'access'; // Sesuaikan nama role di Client Keycloak

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
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm   = env('KEYCLOAK_REALM');

        $apps = Application::all();

        foreach ($apps as $app) {
            $clientUuid = $app->client_id_keycloak;

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