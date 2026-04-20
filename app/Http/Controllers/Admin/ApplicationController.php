<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /**
     * Menampilkan daftar aplikasi
     */
    public function index()
    {
        $applications = Application::all();
        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Menampilkan form tambah aplikasi
     */
    public function create()
    {
        return view('admin.applications.create');
    }

    /**
     * Menampilkan detail aplikasi
     */
    public function show($id)
    {
        $app = Application::findOrFail($id);
        return view('admin.applications.show', compact('app'));
    }

    /**
     * Form Edit Aplikasi
     */
    public function edit($id)
    {
        $app = Application::findOrFail($id);
        return view('admin.applications.edit', compact('app'));
    }

    /**
     * PROSES UPDATE APLIKASI
     */
    public function update(Request $request, $id)
    {
        $app = Application::findOrFail($id);

        $request->validate([
            'app_name' => 'required|string|max:150',
            'url_aplikasi' => 'required|url',
            'status' => 'required|in:active,disabled',
        ]);

        try {
            // Sinkronisasi Perubahan ke Keycloak
            $this->updateInKeycloak($app->client_id, [
                'name' => $request->app_name,
                'enabled' => ($request->status === 'active'),
                'rootUrl' => $request->url_aplikasi,
                'redirectUris' => [$request->url_aplikasi . '/*'],
            ]);

            // Update Database Lokal
            $app->update([
                'app_name' => $request->app_name,
                'url_aplikasi' => $request->url_aplikasi,
                'status' => $request->status,
            ]);

            return redirect()->route('applications.index')->with('success', 'Aplikasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal Update: ' . $e->getMessage());
        }
    }

    /**
     * Simpan Aplikasi Baru (Full Otomatis ke Keycloak)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'redirect_uri' => 'required|url',
            'status' => 'required|in:active,disabled',
        ]);

        try {
            // 1. Generate Client ID unik
            $clientId = Str::slug($request->name) . '-' . Str::lower(Str::random(5));
            
            // 2. Simpan ke Database Lokal
            $app = Application::create([
                'app_name'      => $request->name,
                'client_id'     => $clientId,
                'url_aplikasi'  => $request->redirect_uri,
                'status'        => $request->status,
            ]);

            // 3. Daftarkan ke Keycloak, buat Role, & ambil UUID
           $this->registerToKeycloak($app);

            return redirect()->route('applications.index')->with('success', 'Aplikasi & Client Keycloak berhasil dibuat!');
        } catch (\Exception $e) {
            // Jika gagal di Keycloak, hapus record yang sempat tersimpan di lokal agar tidak duplikat
            if (isset($app)) $app->delete();
            return redirect()->back()->withInput()->with('error', 'Gagal Simpan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Aplikasi dari DB Lokal & Keycloak
     */
    public function destroy($id)
    {
        $app = Application::findOrFail($id);
        try {
            // Hapus di Keycloak dulu
            $this->deleteFromKeycloak($app->client_id);
            
            // Hapus di Lokal
            $app->delete();
            
            return redirect()->route('applications.index')->with('success', 'Aplikasi dihapus dari sistem & Keycloak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Hapus: ' . $e->getMessage());
        }
    }

    /* |--------------------------------------------------------------------------
    | KEYCLOAK PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    private function getAdminToken()
    {
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $response = Http::asForm()->post("{$baseUrl}/realms/master/protocol/openid-connect/token", [
            'grant_type' => 'password',
            'client_id'  => 'admin-cli',
            'username'   => env('KEYCLOAK_ADMIN_USER'),
            'password'   => env('KEYCLOAK_ADMIN_PASS'),
        ]);

        if ($response->failed()) throw new \Exception("Gagal mengambil Admin Token Keycloak.");
        return $response->json()['access_token'];
    }

    private function registerToKeycloak($app)
    {
        $token = $this->getAdminToken();
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');

        // 1. Buat Client Baru di Keycloak
        $createResponse = Http::withToken($token)->post("{$baseUrl}/admin/realms/{$realm}/clients", [
            'clientId'     => $app->client_id,
            'name'         => $app->app_name,
            'enabled'      => ($app->status === 'active'),
            'publicClient' => false,
            'rootUrl'      => $app->url_aplikasi,
            'redirectUris' => [$app->url_aplikasi . '/*'],
        ]);

        if ($createResponse->failed()) throw new \Exception("Gagal membuat Client di Keycloak.");

        // 2. Ambil UUID Internal Keycloak
        $clients = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/clients", ['clientId' => $app->client_id])->json();
        $internalId = $clients[0]['id'];

        // 3. Ambil Client Secret
        $secretResponse = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/clients/{$internalId}/client-secret");
        $secret = $secretResponse->json()['value'] ?? null;

        // 4. Buat Role 'access' secara otomatis
        Http::withToken($token)->post("{$baseUrl}/admin/realms/{$realm}/clients/{$internalId}/roles", [
            'name' => 'access',
            'description' => 'Akses otomatis aplikasi'
        ]);

        // 5. Update data lokal dengan UUID dan Secret
        $app->update([
            'client_secret' => $secret,
            'keycloak_client_uuid' => $internalId
        ]);
    }

    private function updateInKeycloak($clientId, $data)
    {
        $token = $this->getAdminToken();
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');

        $clients = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/clients", ['clientId' => $clientId])->json();
        if (!empty($clients)) {
            $internalId = $clients[0]['id'];
            Http::withToken($token)->put("{$baseUrl}/admin/realms/{$realm}/clients/{$internalId}", $data);
        }
    }

    private function deleteFromKeycloak($clientId)
    {
        $token = $this->getAdminToken();
        $baseUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');

        $clients = Http::withToken($token)->get("{$baseUrl}/admin/realms/{$realm}/clients", ['clientId' => $clientId])->json();
        if (!empty($clients)) {
            $internalId = $clients[0]['id'];
            Http::withToken($token)->delete("{$baseUrl}/admin/realms/{$realm}/clients/{$internalId}");
        }
    }
}