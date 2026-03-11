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
    Schema::create('applications', function (Blueprint $table) {
        $table->increments('id'); // INT AUTO_INCREMENT
        $table->string('app_name', 150)->comment('Nama Aplikasi');
        $table->string('client_id', 150)->comment('ID OIDC');
        $table->string('url_aplikasi', 255)->nullable()->comment('URL tujuan');
        $table->string('icon_aplikasi', 255)->nullable()->comment('File icon');
    });
}

public function down(): void
{
    Schema::dropIfExists('applications');
}
};
