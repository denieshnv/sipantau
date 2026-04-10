<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenSpj extends Model
{
    use HasFactory;

    protected $table = 'dokumen_spjs';

    protected $fillable = [
        'subkegiatan_id',
        'user_id',
        'file_path',
        'tanggal_pelaksanaan',
        'status_validasi',
        'catatan_validasi',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelaksanaan' => 'date',
            'validated_at' => 'datetime',
        ];
    }

    public function subkegiatan()
    {
        return $this->belongsTo(Subkegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke user yang melakukan validasi (kasubag/superadmin).
     * Menggunakan koneksi master karena users di DB master.
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /* ---- Helper status ---- */

    public function isBelumDivalidasi(): bool
    {
        return $this->status_validasi === 'belum_divalidasi';
    }

    public function isSudahDivalidasi(): bool
    {
        return $this->status_validasi === 'sudah_divalidasi';
    }

    public function isPerluPerbaikan(): bool
    {
        return $this->status_validasi === 'perlu_perbaikan';
    }

    /**
     * Label status yang ramah pengguna.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_validasi) {
            'sudah_divalidasi' => 'Sudah Divalidasi',
            'perlu_perbaikan'  => 'Perlu Perbaikan',
            default            => 'Belum Divalidasi',
        };
    }

    /**
     * CSS badge class berdasar status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_validasi) {
            'sudah_divalidasi' => 'bg-success-subtle text-success',
            'perlu_perbaikan'  => 'bg-warning-subtle text-warning',
            default            => 'bg-secondary-subtle text-secondary',
        };
    }

    /**
     * Icon Bootstrap berdasar status.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status_validasi) {
            'sudah_divalidasi' => 'bi-check-circle-fill',
            'perlu_perbaikan'  => 'bi-exclamation-triangle-fill',
            default            => 'bi-hourglass-split',
        };
    }
}
