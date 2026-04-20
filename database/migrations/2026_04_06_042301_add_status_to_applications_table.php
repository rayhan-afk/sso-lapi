<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // 1. Tambahkan status jika belum ada (atau modifikasi)
            if (!Schema::hasColumn('applications', 'status')) {
                $table->string('status', 20)->default('active')->after('icon_aplikasi');
            }

            // 2. Tambahkan client_secret jika belum ada
            if (!Schema::hasColumn('applications', 'client_secret')) {
                $table->text('client_secret')->nullable()->after('client_id');
            }

            // 3. TAMBAHAN WAJIB: Kolom UUID Internal Keycloak
            // Ini untuk menyimpan ID rahasia dari Keycloak agar sinkronisasi Role otomatis jalan
            if (!Schema::hasColumn('applications', 'keycloak_client_uuid')) {
                $table->char('keycloak_client_uuid', 36)->nullable()->after('client_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['status', 'client_secret', 'keycloak_client_uuid']);
        });
    }
};