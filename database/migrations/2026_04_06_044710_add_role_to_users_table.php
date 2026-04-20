<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Menambahkan kolom role setelah kolom jabatan
        // Defaultnya 'user' agar aman jika ada data lama
        $table->string('role', 50)->default('user')->after('jabatan');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // Menghapus kolom jika kita melakukan rollback
        $table->dropColumn('role');
    });
}
};
