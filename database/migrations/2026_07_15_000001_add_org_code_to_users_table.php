<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ORG_CODE dipakai buat ngunci data yang boleh dilihat/diakses Karyawan
 * (cuma data dengan ORG_CODE yang sama dengan milik mereka). Nullable karena
 * Admin gak butuh ini (Admin akses global, semua ORG_CODE).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('org_code')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('org_code');
        });
    }
};
