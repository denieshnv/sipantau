<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\Subkegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function showForm()
    {
        return view('superadmin.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                $this->importCsv($file);
            } else {
                return back()->with('error', 'Format file harus CSV. Untuk file Excel (.xlsx/.xls), silakan export ke CSV terlebih dahulu.');
            }

            return redirect()->route('superadmin.programs.index')
                ->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    private function importCsv($file)
    {
        $handle = fopen($file->getRealPath(), 'r');

        // Baca header
        $headerRaw = fgets($handle);
        $header = $this->parseCsvLine($headerRaw);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        $headerCount = count($header);

        // Cek apakah CSV berformat header (program/kegiatan/subkegiatan)
        // atau langsung data (tanpa header)
        $hasHeader = in_array('program', $header) || in_array('nama_program', $header)
            || in_array('kegiatan', $header) || in_array('nama_kegiatan', $header);

        if (!$hasHeader) {
            // Tidak ada header, kembali ke awal file
            rewind($handle);
            $headerCount = 3; // Asumsikan 3 kolom: program, kegiatan, subkegiatan
        }

        DB::beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $line = rtrim($line, "\r\n");
                if (trim($line) === '') continue;

                $row = $this->parseCsvLine($line);
                $row = array_map('trim', $row);

                $colCount = count($row);
                if ($colCount < 3) continue;

                if ($colCount === $headerCount && $hasHeader) {
                    // Jumlah kolom sesuai header - gunakan nama kolom
                    $data = array_combine($header, $row);
                    $namaProgram     = $data['program']        ?? $data['nama_program']     ?? $row[0];
                    $namaKegiatan    = $data['kegiatan']       ?? $data['nama_kegiatan']    ?? $row[1];
                    $namaSubkegiatan = $data['subkegiatan']    ?? $data['nama_subkegiatan'] ?? $row[2];
                } elseif ($colCount > 3) {
                    // Lebih dari 3 kolom: kemungkinan ada koma dalam nama kegiatan (tanpa kutip)
                    // Kolom pertama = program, kolom terakhir = subkegiatan, tengah = kegiatan
                    $namaProgram     = $row[0];
                    $namaSubkegiatan = $row[$colCount - 1];
                    $namaKegiatan    = implode(', ', array_slice($row, 1, $colCount - 2));
                } else {
                    // Tepat 3 kolom
                    $namaProgram     = $row[0];
                    $namaKegiatan    = $row[1];
                    $namaSubkegiatan = $row[2];
                }

                $tahun = (int) session('selected_year', date('Y'));

                $program = Program::firstOrCreate([
                    'nama_program' => $namaProgram,
                    'tahun' => $tahun,
                ]);

                $kegiatan = Kegiatan::firstOrCreate([
                    'program_id'    => $program->id,
                    'nama_kegiatan' => $namaKegiatan,
                ]);

                Subkegiatan::firstOrCreate([
                    'kegiatan_id'      => $kegiatan->id,
                    'nama_subkegiatan' => $namaSubkegiatan,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        fclose($handle);
    }

    /**
     * Parse satu baris CSV dengan benar, menangani koma dalam tanda kutip.
     * str_getcsv sudah menangani kasus dengan dan tanpa enclosure secara native.
     */
    private function parseCsvLine(string $line): array
    {
        $line = rtrim($line, "\r\n");
        // str_getcsv menangani quoted fields ("nilai, dengan koma") secara otomatis
        return str_getcsv($line, ',', '"');
    }
}
