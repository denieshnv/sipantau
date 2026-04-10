<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom 'tahun' ke tabel programs.
 * Kolom ini menggantikan arsitektur multi-database per tahun
 * sehingga semua data cukup di 1 database, difilter lewat kolom ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->unsignedSmallInteger('tahun')
                  ->default(now()->year)
                  ->after('nama_program')
                  ->index();
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};
