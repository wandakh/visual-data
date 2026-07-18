<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Akun user (Karyawan/Admin) sekarang bisa "dinonaktifkan" (soft delete)
 * kalau resign, bukan dihapus permanen. Efeknya: gak bisa login lagi
 * (Laravel otomatis exclude user yang soft-deleted dari query Auth::attempt),
 * TAPI riwayat di Log Data & Log Login tetap nyambung ke nama mereka
 * (gak jadi null/hilang).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
