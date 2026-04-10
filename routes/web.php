<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\ProgramController;
use App\Http\Controllers\Superadmin\KegiatanController;
use App\Http\Controllers\Superadmin\SubkegiatanController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\ImportController;
use App\Http\Controllers\Superadmin\YearController;
use App\Http\Controllers\Admin\DokumenSpjController;
use App\Http\Controllers\Camat\DashboardController as CamatDashboardController;

// ============================
// Public Routes
// ============================
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================
// Superadmin Routes (superadmin only)
// ============================
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // User Management — hanya superadmin
    Route::resource('users', UserController::class)->except(['show']);

    // Kelola Tahun — hanya superadmin
    Route::get('/tahun', [YearController::class, 'index'])->name('tahun.index');
    Route::post('/tahun', [YearController::class, 'store'])->name('tahun.store');
    Route::put('/tahun/default', [YearController::class, 'setDefault'])->name('tahun.default');
    Route::delete('/tahun', [YearController::class, 'destroy'])->name('tahun.destroy');
});

// ============================
// Superadmin & Kasubag PK Shared Routes
// ============================
Route::middleware(['auth', 'role:superadmin,kasubag_pk'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Master
    Route::resource('programs', ProgramController::class)->except(['show']);
    Route::resource('kegiatans', KegiatanController::class)->except(['show']);
    Route::resource('subkegiatans', SubkegiatanController::class)->except(['show']);

    // Import
    Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'import'])->name('import.process');

    // Lihat semua dokumen & Validasi
    Route::get('/dokumen', [DokumenSpjController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{dokumen}/validasi', [DokumenSpjController::class, 'showValidasi'])->name('dokumen.validasi');
    Route::put('/dokumen/{dokumen}/validasi', [DokumenSpjController::class, 'updateValidasi'])->name('dokumen.validasi.update');
});

// ============================
// Kasubag PK Routes (alias untuk redirect)
// ============================
Route::middleware(['auth', 'role:kasubag_pk'])->prefix('kasubag')->name('kasubag.')->group(function () {
    Route::get('/dokumen', [DokumenSpjController::class, 'index'])->name('dokumen.index');
});

// ============================
// PPTK Routes (ex Admin)
// ============================
Route::middleware(['auth', 'role:pptk'])->prefix('pptk')->name('pptk.')->group(function () {
    Route::get('/dokumen', [DokumenSpjController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/create', [DokumenSpjController::class, 'create'])->name('dokumen.create');
    Route::post('/dokumen', [DokumenSpjController::class, 'store'])->name('dokumen.store');
    Route::delete('/dokumen/{dokumen}', [DokumenSpjController::class, 'destroy'])->name('dokumen.destroy');

    // Perbaiki / Re-upload SPJ yang perlu perbaikan
    Route::post('/dokumen/{dokumen}/perbaiki', [DokumenSpjController::class, 'perbaiki'])->name('dokumen.perbaiki');
});

// ============================
// Camat Routes
// ============================
Route::middleware(['auth', 'role:camat'])->prefix('camat')->name('camat.')->group(function () {
    Route::get('/dashboard', [CamatDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dokumen', [DokumenSpjController::class, 'index'])->name('dokumen.index');
});

// ============================
// Download Routes (semua role yang login)
// ============================
Route::middleware(['auth'])->group(function () {
    Route::get('/dokumen/{dokumen}/download', [DokumenSpjController::class, 'download'])->name('dokumen.download');
});

// Download Semua SPJ (ZIP) — superadmin, kasubag_pk, camat
Route::middleware(['auth', 'role:superadmin,kasubag_pk,camat'])->group(function () {
    Route::get('/dokumen/download-all', [DokumenSpjController::class, 'downloadAll'])->name('dokumen.download-all');
});

// ============================
// API for Cascading Dropdowns
// ============================
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/kegiatans/{program_id}', function ($program_id) {
        // Pastikan program milik tahun yang dipilih
        $program = \App\Models\Program::forSelectedYear()->find($program_id);
        if (!$program) {
            return response()->json([]);
        }
        return \App\Models\Kegiatan::where('program_id', $program_id)
            ->orderBy('nama_kegiatan')->get(['id', 'nama_kegiatan']);
    })->name('api.kegiatans');

    Route::get('/subkegiatans/{kegiatan_id}', function ($kegiatan_id) {
        return \App\Models\Subkegiatan::where('kegiatan_id', $kegiatan_id)
            ->orderBy('nama_subkegiatan')->get(['id', 'nama_subkegiatan']);
    })->name('api.subkegiatans');
});
