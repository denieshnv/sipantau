<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function dokumenSpjs()
    {
        return $this->hasMany(DokumenSpj::class);
    }

    /* ---- Role Checks ---- */

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isKasubagPk(): bool
    {
        return $this->role === 'kasubag_pk';
    }

    public function isPptk(): bool
    {
        return $this->role === 'pptk';
    }

    public function isCamat(): bool
    {
        return $this->role === 'camat';
    }

    /* ---- Permission Helpers ---- */

    /**
     * Bisa mengelola data master (program, kegiatan, subkegiatan), import, dashboard monitoring.
     */
    public function canManageData(): bool
    {
        return in_array($this->role, ['superadmin', 'kasubag_pk']);
    }

    /**
     * Bisa melakukan validasi dokumen SPJ.
     */
    public function canValidate(): bool
    {
        return in_array($this->role, ['superadmin', 'kasubag_pk']);
    }

    /**
     * Bisa download semua SPJ sekaligus (ZIP).
     */
    public function canDownloadAll(): bool
    {
        return in_array($this->role, ['superadmin', 'kasubag_pk', 'camat']);
    }

    /**
     * Bisa upload / perbaiki SPJ.
     */
    public function canUploadSpj(): bool
    {
        return $this->role === 'pptk';
    }

    /**
     * Bisa kelola user.
     */
    public function canManageUsers(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Bisa kelola tahun anggaran.
     */
    public function canManageYears(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Bisa melihat semua dokumen SPJ (lintas user).
     */
    public function canViewAllDocuments(): bool
    {
        return in_array($this->role, ['superadmin', 'kasubag_pk', 'camat']);
    }

    /**
     * Label role yang ramah pengguna.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superadmin'  => 'Super Admin',
            'kasubag_pk'  => 'Kasubag PK',
            'pptk'        => 'PPTK',
            'camat'       => 'Camat',
            default       => ucfirst($this->role),
        };
    }
}
