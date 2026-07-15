<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data dihapus sekarang gak langsung hilang permanen — cuma ditandain
 * `deleted_at` (soft delete). Bisa dipulihin lewat halaman "Data Terhapus".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('databases', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('databases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
