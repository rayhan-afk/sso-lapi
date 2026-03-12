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
    Schema::create('user_otps', function (Blueprint $table) {
        $table->id()->comment('Auto Increment'); // BIGINT AUTO_INCREMENT
        $table->uuid('user_id')->comment('Referensi users.id');
        $table->string('purpose', 50)->nullable()->comment('LOGIN, VERIFY_EMAIL, RESET_PW');
        $table->string('code', 10)->comment('Kode OTP');
        $table->string('sent_to', 150)->nullable()->comment('Email tujuan');
        $table->timestamp('expires_at')->nullable()->comment('Waktu kadaluwarsa');
        $table->integer('attempts')->default(0)->comment('Jumlah percobaan input');
        $table->timestamp('used_at')->nullable()->comment('Waktu digunakan');
        $table->timestamp('created_at')->useCurrent()->comment('Waktu pembuatan');
        $table->string('ip', 50)->nullable()->comment('IP pengirim permintaan');
        $table->timestamp('resend_after')->nullable()->comment('Waktu boleh minta ulang');

        // Foreign Key
        $table->foreign('user_id', 'fk_otp_user')->references('id')->on('users')->onDelete('cascade');

        // Index
        $table->index('user_id', 'idx_otp_user');
    });
}

public function down(): void
{
    Schema::dropIfExists('user_otps');
}
};
