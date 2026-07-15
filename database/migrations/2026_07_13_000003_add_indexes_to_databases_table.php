<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index buat kolom yang paling sering dipakai filter/urutkan (NAMA_CUSTOMER,
 * Tanggal). Tabel ini isinya 200 ribu+ baris — tanpa index, query filter di
 * dashboard/diagram bakal makin lambat seiring data nambah banyak.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('databases', function (Blueprint $table) {
            $table->index('NAMA_CUSTOMER');
            $table->index('Tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('databases', function (Blueprint $table) {
            $table->dropIndex(['NAMA_CUSTOMER']);
            $table->dropIndex(['Tanggal']);
        });
    }
};
