<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom foto profil ke tabel users.
 *
 * CATATAN:
 * - Kolom `username` TIDAK dibawa lagi karena di project lama kolom ini sempat
 *   ditambahkan lalu dihapus di migration berikutnya (net effect: tidak ada).
 *   AdminController lama yang mengecek kolom ini karenanya selalu gagal — sudah dihapus.
 * - Kolom `role` & `role_id` (yang dulu sempat ada di sini) TIDAK dibawa sama
 *   sekali — sistem role 100% lewat Spatie (tabel roles/model_has_roles),
 *   dua kolom itu cuma bakal jadi duplikasi yang gak kepakai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
