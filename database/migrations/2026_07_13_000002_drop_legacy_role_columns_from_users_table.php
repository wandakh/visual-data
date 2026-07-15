<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom `role` (string) dan `role_id` (FK) sudah gak dipakai di logic
 * aplikasi manapun — sistem role yang aktif 100% lewat Spatie
 * (tabel roles/model_has_roles). Migration ini menghapus dua kolom itu
 * KALAU ada.
 *
 * Ditulis defensif (cek Schema::hasColumn dulu) supaya aman dijalankan di
 * dua kondisi: database yang migration awalnya (add_profile_fields...)
 * sempat menambahkan role/role_id, MAUPUN database baru yang migration
 * awalnya udah gak nambahin kolom itu sama sekali (gak akan error
 * "column doesn't exist").
 */
return new class extends Migration
{
    public function up(): void
    {
        $adaKolom = array_filter(
            ['role', 'role_id'],
            fn (string $kolom) => Schema::hasColumn('users', $kolom)
        );

        if (empty($adaKolom)) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($adaKolom) {
            $table->dropColumn($adaKolom);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('image');
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('role');
            }
        });
    }
};
