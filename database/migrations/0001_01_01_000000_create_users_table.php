<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel users dengan field untuk RBAC dan manajemen login gagal.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key sequential (angka) - intentional untuk simulasi IDOR
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // RBAC: role pengguna, admin-pemda, atau superadmin
            $table->enum('role', ['pengguna', 'admin-pemda', 'superadmin'])->default('pengguna');

            // Manajemen Login Gagal: counter percobaan login dan waktu blokir
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel token reset password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel sesi login (Manajemen Sesi)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
