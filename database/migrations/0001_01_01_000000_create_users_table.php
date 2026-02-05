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
        // Tabel Users (End User / Customer)
        // Tanpa Prefix 'laravel_' karena akan di-share dengan Backend Core (Golang/Rust)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Password Reset Tokens (Admin & User)
        // Pakai Prefix 'laravel_' karena ini bawaan framework Laravel
        Schema::create('laravel_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();
        });

        // Note: Tabel Sessions dipisah ke file migration tersendiri (create_cms_sessions_table)
        // agar lebih modular dan prefix-nya terkontrol (laravel_cms_sessions).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('laravel_password_reset_tokens');
    }
};
