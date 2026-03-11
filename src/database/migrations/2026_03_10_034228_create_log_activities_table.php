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
    Schema::create('log_activities', function (Blueprint $table) {
        $table->id(); // BIGINT AUTO_INCREMENT
        $table->uuid('user_id')->comment('Referensi users.id');
        $table->unsignedInteger('app_id')->comment('Referensi applications.id');
        $table->string('action_type', 100)->nullable()->comment('LOGIN_SSO, dsb');
        $table->text('description')->nullable();
        $table->string('ip_address', 50)->nullable();
        $table->string('device', 150)->nullable();
        $table->string('location', 150)->nullable();
        $table->timestamp('created_at')->useCurrent();

        // Foreign Keys
        $table->foreign('user_id', 'fk_log_user')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('app_id', 'fk_log_app')->references('id')->on('applications')->onDelete('cascade');

        // Indexes
        $table->index('user_id', 'idx_log_user');
        $table->index('app_id', 'idx_log_app');
    });
}

public function down(): void
{
    Schema::dropIfExists('log_activities');
}
};
