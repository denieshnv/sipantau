<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom validasi ke tabel dokumen_spjs di database utama.
 * Kolom-kolom ini sebelumnya hanya ada di database per-tahun.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_spjs', function (Blueprint $table) {
            $table->enum('status_validasi', ['belum_divalidasi', 'sudah_divalidasi', 'perlu_perbaikan'])
                  ->default('belum_divalidasi')
                  ->after('tanggal_pelaksanaan');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->unsignedBigInteger('validated_by')->nullable()->after('catatan_validasi');
            $table->timestamp('validated_at')->nullable()->after('validated_by');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_spjs', function (Blueprint $table) {
            $table->dropColumn(['status_validasi', 'catatan_validasi', 'validated_by', 'validated_at']);
        });
    }
};
