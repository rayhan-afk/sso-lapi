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
    Schema::create('user_app_access', function (Blueprint $table) {
        $table->increments('id'); // INT AUTO_INCREMENT
        $table->uuid('user_id')->comment('Referensi users.id');
        $table->unsignedInteger('app_id')->comment('Referensi applications.id');

        // Foreign Keys
        $table->foreign('user_id', 'fk_user_access_user')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('app_id', 'fk_user_access_app')->references('id')->on('applications')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('user_app_access');
}
};
