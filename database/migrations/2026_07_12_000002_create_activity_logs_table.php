<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catatan siapa-ngapain-kapan. Dibuat sederhana (bukan package terpisah)
 * karena kebutuhannya masih basic: siapa melakukan aksi apa, ke data mana,
 * dan kapan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created | updated | deleted | restored | imported
            $table->unsignedBigInteger('subject_id')->nullable(); // id baris di tabel databases (nullable krn 'imported' bisa mewakili banyak baris)
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
