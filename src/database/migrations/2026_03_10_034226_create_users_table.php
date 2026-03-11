<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID unik global (User ID)');
            $table->string('nama', 150)->comment('Nama Lengkap'); // <-- Tetap 'nama'
            $table->string('email', 150)->unique()->comment('Username/Email');
            $table->string('password', 255)->comment('Hash password');
            $table->string('jabatan', 100)->nullable()->comment('Posisi');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            
            // HAPUS created_at lama, GANTI dengan ini agar updated_at juga dibuat otomatis
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};