<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mengubah enum role di tabel users:
 *   Lama: superadmin, admin
 *   Baru: superadmin, kasubag_pk, pptk, camat
 *
 * User dengan role 'admin' akan diubah ke 'pptk'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand the ENUM string first so it accepts the new values without warning
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('superadmin','admin','kasubag_pk','pptk','camat') NOT NULL DEFAULT 'pptk'");

        // 2. Sekarang update data lama
        DB::table('users')->where('role', 'admin')->update(['role' => 'pptk']);

        // 3. Hapus 'admin' dari ENUM jika MySQL memungkinkan, atau cukup timpa dengan yang baru
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('superadmin','kasubag_pk','pptk','camat') NOT NULL DEFAULT 'pptk'");
    }

    public function down(): void
    {
        // Rollback: ubah pptk → admin, hapus enum baru
        DB::table('users')->where('role', 'pptk')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'kasubag_pk')->update(['role' => 'superadmin']);
        DB::table('users')->where('role', 'camat')->update(['role' => 'admin']);

        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('superadmin','admin') NOT NULL DEFAULT 'admin'");
    }
};
