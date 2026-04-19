<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mengubah kolom file_path dari VARCHAR(255) ke TEXT agar bisa menyimpan
 * path file yang panjang (terutama untuk subkegiatan dengan nama panjang).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_spjs', function (Blueprint $table) {
            $table->text('file_path')->change();
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_spjs', function (Blueprint $table) {
            $table->string('file_path')->change();
        });
    }
};
