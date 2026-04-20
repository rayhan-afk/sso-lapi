<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'required|in:user,admin',
            'status'   => 'required|in:active,disabled',
            'apps'     => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $token = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_BASE_URL');
            $realm   = env('KEYCLOAK_REALM');
            
            // 1. Create User di Keycloak
            $payload = [
                'username'      => $request->email,
                'email'         => $request->email,
                'enabled'       => ($request->status === 'active'),
                'firstName'     => $request->name,
                'emailVerified' => true,
                'credentials'   => [['type' => 'password', 'value' => $request->password, 'temporary' => false]]
            ];

            $response = Http::withToken($token)->post("{$baseUrl}/admin/realms/{$realm}/users", $payload);

            if ($response->failed()) throw new \Exception("Keycloak Store Error: " . $response->body());

            // 2. Ambil UUID yang digenerate Keycloak
            $search = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/users", ['email' => $request->email])->json();
            $uuid = $search[0]['id'];

            // 3. Simpan di Database Lokal
            $user = User::create([
                'id'        => $uuid,
                'nama'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'jabatan'   => $request->role,
                'is_active' => ($request->status === 'active' ? 1 : 0),
            ]);

            // 4. Sinkronisasi Akses Aplikasi (Lokal & Keycloak)
            if ($request->has('apps')) {
                $user->applications()->sync($request->apps);
                $this->syncKeycloakRoles($uuid, $request->apps);
            }

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User berhasil didaftarkan ke Sistem & Keycloak!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Form Edit User.
     */
    public function edit($id)
    {
        $user = User::with('applications')->findOrFail($id);
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
            'name'   => 'required|string|max:150',
            'role'   => 'required|in:user,admin',
            'status' => 'required|in:active,disabled',
            'apps'   => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $token = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_BASE_URL');
            $realm   = env('KEYCLOAK_REALM');

            // 1. Update Profile & Status di Keycloak
            $payload = [
                'firstName' => $request->name,
                'enabled'   => ($request->status === 'active'),
            ];
            Http::withToken($token)->put("{$baseUrl}/admin/realms/{$realm}/users/{$id}", $payload);

            // Update Password jika diisi
            if ($request->filled('password')) {
                Http::withToken($token)->put("{$baseUrl}/admin/realms/{$realm}/users/{$id}/reset-password", [
                    'type' => 'password', 'value' => $request->password, 'temporary' => false
                ]);
            }

            // 2. Update Database Lokal
            $user->update([
                'nama'      => $request->name,
                'jabatan'   => $request->role,
                'is_active' => ($request->status === 'active' ? 1 : 0),
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // 3. Sinkronisasi Role Mapping Keycloak
            // Kita hapus semua akses lama di Keycloak dulu, baru tambah yang baru
            $this->clearAllKeycloakClientRoles($id);
            
            $selectedApps = $request->apps ?? [];
            $user->applications()->sync($selectedApps);

            if (!empty($selectedApps)) {
                $this->syncKeycloakRoles($id, $selectedApps);
            }

            DB::commit();
            return redirect()->route('users.index')->with('success', 'Data user dan akses Keycloak berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal Update: " . $e->getMessage());
        }
    }

    /**
     * Helper: Sinkronisasi Role ke Keycloak.
     */
    private function syncKeycloakRoles($userId, $appIds)
    {
        $token = $this->getKeycloakAdminToken();
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');

        $apps = Application::whereIn('id', $appIds)->get();

        foreach ($apps as $app) {
            // Pastikan kolom client_id_keycloak berisi UUID Client dari Keycloak
            $clientUuid = $app->client_id_keycloak; 
            $roleName = 'access'; // Sesuaikan nama role yang kamu buat di Client Keycloak

            // Ambil detail Role dari Keycloak
            $roleResponse = Http::withToken($token)
                ->get("{$baseUrl}/admin/realms/{$realm}/clients/{$clientUuid}/roles/{$roleName}");

            if ($roleResponse->successful()) {
                $roleData = $roleResponse->json();
                
                // Assign Role ke User
                Http::withToken($token)->post("{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/clients/{$clientUuid}", [
                    [
                        'id'   => $roleData['id'],
                        'name' => $roleData['name']
                    ]
                ]);
            }
        }
    }

    /**
     * Helper: Hapus semua Client Roles user sebelum update (Reset).
     */
    private function clearAllKeycloakClientRoles($userId)
    {
        $token = $this->getKeycloakAdminToken();
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');

        // Ambil semua aplikasi untuk tahu client mana saja yang harus dibersihkan
        $apps = Application::all();

        foreach ($apps as $app) {
            $clientUuid = $app->client_id_keycloak;
            
            // Ambil role yang saat ini dimiliki user pada client tersebut
            $currentRoles = Http::withToken($token)
                ->get("{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/clients/{$clientUuid}")
                ->json();

            if (!empty($currentRoles) && is_array($currentRoles)) {
                // Hapus role tersebut
                Http::withToken($token)->delete("{$baseUrl}/admin/realms/{$realm}/users/{$userId}/role-mappings/clients/{$clientUuid}", $currentRoles);
            }
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
     * Hapus User dari Keycloak & DB Lokal.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);

            $token = $this->getKeycloakAdminToken();
            $baseUrl = env('KEYCLOAK_BASE_URL');
            $realm   = env('KEYCLOAK_REALM');

            // 1. Hapus di Keycloak
            Http::withToken($token)->delete("{$baseUrl}/admin/realms/{$realm}/users/{$id}");

            // 2. Hapus Lokal
            $user->applications()->detach();
            $user->delete();

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User berhasil dihapus dari sistem & Keycloak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal Hapus: " . $e->getMessage());
        }
    }

    /**
     * Helper Token Admin.
     */
    private function getKeycloakAdminToken()
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $response = Http::asForm()->post("{$baseUrl}/realms/master/protocol/openid-connect/token", [
            'client_id'  => 'admin-cli',
            'username'   => env('KEYCLOAK_ADMIN_USER'),
            'password'   => env('KEYCLOAK_ADMIN_PASS'),
            'grant_type' => 'password',
        ]);

        if ($response->failed()) throw new \Exception("Gagal Otentikasi ke Server Keycloak.");
        return $response->json()['access_token'];
    }
}