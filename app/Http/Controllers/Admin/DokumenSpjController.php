<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenSpj;
use App\Models\Subkegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DokumenSpjController extends Controller
{
    public function index(Request $request)
    {
        $query = DokumenSpj::with(['subkegiatan.kegiatan.program', 'user', 'validator'])
            ->whereHas('subkegiatan.kegiatan.program', fn($q) => $q->forSelectedYear())
            ->latest();

        // PPTK hanya lihat milik sendiri
        if (!Auth::user()->canViewAllDocuments()) {
            $query->where('user_id', Auth::id());
        }

        // Filter berdasarkan status validasi
        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        $dokumens = $query->paginate(15)->withQueryString();

        return view('admin.dokumen.index', compact('dokumens'));
    }

    public function create()
    {
        $programs = \App\Models\Program::forSelectedYear()->orderBy('nama_program')->get();

        return view('admin.dokumen.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subkegiatan_id' => 'required|exists:subkegiatans,id',
            'tanggal_pelaksanaan' => 'required|date',
            'file' => 'required|file|mimes:pdf|max:5120',
        ], [
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 5MB.',
            'tanggal_pelaksanaan.required' => 'Tanggal pelaksanaan kegiatan wajib diisi.',
        ]);

        $subkegiatan = Subkegiatan::findOrFail($request->subkegiatan_id);

        // Create folder name from subkegiatan name (sanitized)
        $folderName = Str::slug($subkegiatan->nama_subkegiatan, '_');

        // Get tahun from session (selected year at login)
        $tahun = session('selected_year', date('Y'));

        // Hitung nomor urut dokumen untuk subkegiatan ini
        $existingCount = DokumenSpj::where('subkegiatan_id', $subkegiatan->id)->count();
        $nomorUrut = $existingCount + 1;

        // Format: namasubkegiatan_tahun_nomor.pdf
        $namaSubkegiatan = Str::slug($subkegiatan->nama_subkegiatan, '_');
        $fileName = "{$namaSubkegiatan}_{$tahun}_{$nomorUrut}.pdf";

        // Store file dalam folder tahun: spj_documents/{tahun}/{subkegiatan}/file.pdf
        $path = $request->file('file')->storeAs(
            "spj_documents/{$tahun}/{$folderName}",
            $fileName,
            'public'
        );

        DokumenSpj::create([
            'subkegiatan_id' => $request->subkegiatan_id,
            'user_id' => Auth::id(),
            'file_path' => $path,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'status_validasi' => 'belum_divalidasi',
        ]);

        return redirect()->route('pptk.dokumen.index')
            ->with('success', 'Dokumen SPJ berhasil diunggah.');
    }

    public function download(DokumenSpj $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($dokumen->file_path);
    }

    public function destroy(DokumenSpj $dokumen)
    {
        if ($dokumen->user_id !== Auth::id() && !Auth::user()->canManageData()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return back()->with('success', 'Dokumen SPJ berhasil dihapus.');
    }

    /* ======================================================
     * DOWNLOAD ALL — ZIP semua SPJ tahun terpilih
     * ====================================================== */

    /**
     * Download semua dokumen SPJ tahun terpilih dalam bentuk ZIP.
     */
    public function downloadAll()
    {
        if (!Auth::user()->canDownloadAll()) {
            abort(403, 'Anda tidak memiliki akses untuk download semua dokumen.');
        }

        $tahun = (int) session('selected_year', date('Y'));

        $dokumens = DokumenSpj::with('subkegiatan.kegiatan.program')
            ->whereHas('subkegiatan.kegiatan.program', fn($q) => $q->forSelectedYear())
            ->get();

        if ($dokumens->isEmpty()) {
            return back()->with('error', 'Tidak ada dokumen SPJ untuk tahun ' . $tahun . '.');
        }

        // Buat file ZIP sementara
        $zipFileName = "SPJ_Tahun_{$tahun}_" . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path("app/temp/{$zipFileName}");

        // Pastikan folder temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $addedFiles = 0;
        foreach ($dokumens as $dokumen) {
            $filePath = Storage::disk('public')->path($dokumen->file_path);

            if (file_exists($filePath)) {
                // Struktur di dalam ZIP: Program/Kegiatan/Subkegiatan/file.pdf
                $program = $dokumen->subkegiatan->kegiatan->program->nama_program ?? 'Program';
                $kegiatan = $dokumen->subkegiatan->kegiatan->nama_kegiatan ?? 'Kegiatan';
                $subkegiatan = $dokumen->subkegiatan->nama_subkegiatan ?? 'Subkegiatan';

                $zipInternalPath = Str::slug($program, '_') . '/'
                    . Str::slug($kegiatan, '_') . '/'
                    . Str::slug($subkegiatan, '_') . '/'
                    . basename($filePath);

                $zip->addFile($filePath, $zipInternalPath);
                $addedFiles++;
            }
        }

        $zip->close();

        if ($addedFiles === 0) {
            @unlink($zipPath);
            return back()->with('error', 'Tidak ada file fisik yang ditemukan untuk di-download.');
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /* ======================================================
     * PERBAIKI — PPTK re-upload SPJ yang perlu perbaikan
     * ====================================================== */

    /**
     * Proses perbaikan (re-upload) dokumen SPJ.
     */
    public function perbaiki(Request $request, DokumenSpj $dokumen)
    {
        // Hanya pemilik dokumen yang boleh memperbaiki
        if ($dokumen->user_id !== Auth::id() && !Auth::user()->canManageData()) {
            abort(403);
        }

        // Hanya dokumen dengan status "perlu_perbaikan" yang bisa diperbaiki
        if (!$dokumen->isPerluPerbaikan()) {
            return back()->with('error', 'Hanya dokumen dengan status "Perlu Perbaikan" yang dapat diperbaiki.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120',
        ], [
            'file.required' => 'File PDF wajib diunggah.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        // Hapus file lama
        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        // Simpan file baru
        $subkegiatan = $dokumen->subkegiatan;
        $folderName = Str::slug($subkegiatan->nama_subkegiatan, '_');
        $tahun = session('selected_year', date('Y'));

        // Gunakan nama file baru dengan suffix _revisi
        $namaSubkegiatan = Str::slug($subkegiatan->nama_subkegiatan, '_');
        $fileName = "{$namaSubkegiatan}_{$tahun}_{$dokumen->id}_revisi.pdf";

        // Store dalam folder tahun
        $path = $request->file('file')->storeAs(
            "spj_documents/{$tahun}/{$folderName}",
            $fileName,
            'public'
        );

        // Update dokumen: file baru, reset status ke belum_divalidasi
        $dokumen->update([
            'file_path' => $path,
            'status_validasi' => 'belum_divalidasi',
            'catatan_validasi' => null,
            'validated_by' => null,
            'validated_at' => null,
        ]);

        return redirect()->route('pptk.dokumen.index')
            ->with('success', 'Dokumen SPJ berhasil diperbaiki dan diunggah ulang. Status kembali ke "Belum Divalidasi".');
    }

    /* ======================================================
     * VALIDASI — Superadmin & Kasubag PK
     * ====================================================== */

    /**
     * Tampilkan halaman detail validasi sebuah dokumen SPJ.
     */
    public function showValidasi(DokumenSpj $dokumen)
    {
        $this->authorizeValidator();
        $dokumen->load(['subkegiatan.kegiatan.program', 'user', 'validator']);

        return view('superadmin.dokumen.validasi', compact('dokumen'));
    }

    /**
     * Proses validasi / penolakan dokumen SPJ.
     */
    public function updateValidasi(Request $request, DokumenSpj $dokumen)
    {
        $this->authorizeValidator();

        $request->validate([
            'status_validasi' => 'required|in:sudah_divalidasi,perlu_perbaikan',
            'catatan_validasi' => 'nullable|string|max:1000',
        ], [
            'status_validasi.required' => 'Status validasi wajib dipilih.',
            'catatan_validasi.max' => 'Catatan validasi maksimal 1000 karakter.',
        ]);

        // Jika perlu perbaikan, catatan wajib diisi
        if ($request->status_validasi === 'perlu_perbaikan' && empty($request->catatan_validasi)) {
            return back()->withErrors(['catatan_validasi' => 'Catatan wajib diisi jika status Perlu Perbaikan.'])->withInput();
        }

        $dokumen->update([
            'status_validasi' => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        $statusLabel = $dokumen->status_label;

        // Redirect sesuai role
        $routeName = Auth::user()->isSuperadmin() ? 'superadmin.dokumen.index' : 'kasubag.dokumen.index';

        return redirect()->route($routeName)
            ->with('success', "Dokumen SPJ berhasil diubah statusnya menjadi \"{$statusLabel}\".");
    }

    /**
     * Guard: hanya yang bisa validasi (superadmin + kasubag_pk).
     */
    private function authorizeValidator(): void
    {
        if (!Auth::user()->canValidate()) {
            abort(403, 'Hanya Kasubag PK atau Super Admin yang dapat melakukan validasi SPJ.');
        }
    }
}
