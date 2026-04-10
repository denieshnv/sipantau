<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Command untuk migrasi data dari database per-tahun (sipantau_2024, sipantau_2025, dll)
 * ke database utama (sipantau) dengan menambahkan kolom 'tahun' ke setiap record program.
 *
 * Jalankan SETELAH migration add_tahun_to_programs_table dieksekusi.
 *
 * Usage:  php artisan sipantau:migrate-to-single-db
 */
class MigrateYearlyDataToSingleDb extends Command
{
    protected $signature = 'sipantau:migrate-to-single-db
                            {--years= : Daftar tahun dipisah koma, contoh: 2024,2025,2026. Jika kosong, ambil dari config.}
                            {--dry-run : Tampilkan ringkasan tanpa benar-benar menyimpan data}';

    protected $description = 'Migrasi data dari database per-tahun ke database utama (single database)';

    public function handle(): int
    {
        $yearsInput = $this->option('years');
        $years = $yearsInput
            ? array_map('intval', explode(',', $yearsInput))
            : config('sipantau.available_years', []);

        if (empty($years)) {
            $this->error('Tidak ada tahun yang ditentukan. Gunakan --years=2024,2025,2026');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $mainDb = env('DB_DATABASE', 'sipantau');

        $this->info("🔄 Migrasi data dari " . count($years) . " database tahunan ke '{$mainDb}'");
        if ($dryRun) {
            $this->warn("   ⚠️  DRY-RUN MODE — data TIDAK akan disimpan");
        }

        foreach ($years as $year) {
            $yearDb = 'sipantau_' . $year;

            // Cek apakah database tahun ini ada
            try {
                $exists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$yearDb]);
                if (empty($exists)) {
                    $this->warn("   ⏩ Database '{$yearDb}' tidak ditemukan, skip.");
                    continue;
                }
            } catch (\Exception $e) {
                $this->warn("   ⏩ Gagal cek database '{$yearDb}': " . $e->getMessage());
                continue;
            }

            $this->info("\n📦 Memproses tahun {$year} (database: {$yearDb})...");

            // Set koneksi sementara ke database tahun ini
            Config::set('database.connections.yearly_temp.driver', 'mysql');
            Config::set('database.connections.yearly_temp.host', env('DB_HOST', '127.0.0.1'));
            Config::set('database.connections.yearly_temp.port', env('DB_PORT', '3306'));
            Config::set('database.connections.yearly_temp.database', $yearDb);
            Config::set('database.connections.yearly_temp.username', env('DB_USERNAME', 'root'));
            Config::set('database.connections.yearly_temp.password', env('DB_PASSWORD', ''));
            Config::set('database.connections.yearly_temp.charset', 'utf8mb4');
            Config::set('database.connections.yearly_temp.collation', 'utf8mb4_unicode_ci');

            DB::purge('yearly_temp');

            try {
                // Cek apakah tabel programs ada di database tahun ini
                $tables = DB::connection('yearly_temp')
                    ->select("SHOW TABLES LIKE 'programs'");

                if (empty($tables)) {
                    $this->warn("   ⏩ Tabel 'programs' tidak ditemukan di '{$yearDb}', skip.");
                    continue;
                }

                $this->migrateYear($year, $dryRun);

            } catch (\Exception $e) {
                $this->error("   ❌ Error pada tahun {$year}: " . $e->getMessage());
                continue;
            }
        }

        DB::purge('yearly_temp');

        $this->newLine();
        $this->info("🎉 Migrasi selesai!");

        if (!$dryRun) {
            $this->info("   Anda sekarang bisa menghapus database per-tahun yang sudah tidak diperlukan.");
            $this->info("   Gunakan: DROP DATABASE sipantau_YYYY;");
        }

        return self::SUCCESS;
    }

    private function migrateYear(int $year, bool $dryRun): void
    {
        // 1. Migrasi Programs
        $programs = DB::connection('yearly_temp')->table('programs')->get();
        $this->info("   📋 Programs: {$programs->count()} records");

        $programIdMap = []; // old_id => new_id

        foreach ($programs as $program) {
            // Cek apakah program sudah ada di DB utama (sama nama + tahun)
            $existing = DB::table('programs')
                ->where('nama_program', $program->nama_program)
                ->where('tahun', $year)
                ->first();

            if ($existing) {
                $programIdMap[$program->id] = $existing->id;
                continue;
            }

            if (!$dryRun) {
                $newId = DB::table('programs')->insertGetId([
                    'nama_program' => $program->nama_program,
                    'tahun' => $year,
                    'created_at' => $program->created_at,
                    'updated_at' => $program->updated_at,
                ]);
                $programIdMap[$program->id] = $newId;
            } else {
                $programIdMap[$program->id] = 'DRY-' . $program->id;
            }
        }

        // 2. Migrasi Kegiatans
        $kegiatans = DB::connection('yearly_temp')->table('kegiatans')->get();
        $this->info("   📋 Kegiatans: {$kegiatans->count()} records");

        $kegiatanIdMap = [];

        foreach ($kegiatans as $kegiatan) {
            $newProgramId = $programIdMap[$kegiatan->program_id] ?? null;
            if (!$newProgramId || $dryRun) {
                $kegiatanIdMap[$kegiatan->id] = 'DRY-' . $kegiatan->id;
                continue;
            }

            $existing = DB::table('kegiatans')
                ->where('program_id', $newProgramId)
                ->where('nama_kegiatan', $kegiatan->nama_kegiatan)
                ->first();

            if ($existing) {
                $kegiatanIdMap[$kegiatan->id] = $existing->id;
                continue;
            }

            $newId = DB::table('kegiatans')->insertGetId([
                'program_id' => $newProgramId,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'created_at' => $kegiatan->created_at,
                'updated_at' => $kegiatan->updated_at,
            ]);
            $kegiatanIdMap[$kegiatan->id] = $newId;
        }

        // 3. Migrasi Subkegiatans
        $subkegiatans = DB::connection('yearly_temp')->table('subkegiatans')->get();
        $this->info("   📋 Subkegiatans: {$subkegiatans->count()} records");

        $subkegiatanIdMap = [];

        foreach ($subkegiatans as $sub) {
            $newKegiatanId = $kegiatanIdMap[$sub->kegiatan_id] ?? null;
            if (!$newKegiatanId || $dryRun) {
                $subkegiatanIdMap[$sub->id] = 'DRY-' . $sub->id;
                continue;
            }

            $existing = DB::table('subkegiatans')
                ->where('kegiatan_id', $newKegiatanId)
                ->where('nama_subkegiatan', $sub->nama_subkegiatan)
                ->first();

            if ($existing) {
                $subkegiatanIdMap[$sub->id] = $existing->id;
                continue;
            }

            $newId = DB::table('subkegiatans')->insertGetId([
                'kegiatan_id' => $newKegiatanId,
                'nama_subkegiatan' => $sub->nama_subkegiatan,
                'created_at' => $sub->created_at,
                'updated_at' => $sub->updated_at,
            ]);
            $subkegiatanIdMap[$sub->id] = $newId;
        }

        // 4. Migrasi Dokumen SPJs
        $hasDokumenTable = DB::connection('yearly_temp')
            ->select("SHOW TABLES LIKE 'dokumen_spjs'");

        if (!empty($hasDokumenTable)) {
            $dokumens = DB::connection('yearly_temp')->table('dokumen_spjs')->get();
            $this->info("   📋 Dokumen SPJs: {$dokumens->count()} records");

            foreach ($dokumens as $dok) {
                $newSubId = $subkegiatanIdMap[$dok->subkegiatan_id] ?? null;
                if (!$newSubId || $dryRun) continue;

                // Cek duplikasi by file_path
                $existing = DB::table('dokumen_spjs')
                    ->where('file_path', $dok->file_path)
                    ->first();

                if ($existing) continue;

                $data = [
                    'subkegiatan_id' => $newSubId,
                    'user_id' => $dok->user_id,
                    'file_path' => $dok->file_path,
                    'tanggal_pelaksanaan' => $dok->tanggal_pelaksanaan,
                    'created_at' => $dok->created_at,
                    'updated_at' => $dok->updated_at,
                ];

                // Tambahkan kolom validasi jika ada
                if (isset($dok->status_validasi)) {
                    $data['status_validasi'] = $dok->status_validasi;
                    $data['catatan_validasi'] = $dok->catatan_validasi ?? null;
                    $data['validated_by'] = $dok->validated_by ?? null;
                    $data['validated_at'] = $dok->validated_at ?? null;
                }

                DB::table('dokumen_spjs')->insert($data);
            }
        } else {
            $this->warn("   ⏩ Tabel 'dokumen_spjs' tidak ditemukan di database tahun ini");
        }

        $this->info("   ✅ Tahun {$year} selesai.");
    }
}
