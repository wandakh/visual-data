<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Terpisah dari `activity_logs` (yang nyatet perubahan DATA — tambah/edit/
 * hapus/pulihkan/import). Tabel ini khusus nyatet aktivitas AKUN/SESI user:
 * login, logout, export data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // login | logout | export
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
