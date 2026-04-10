<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['nama_program', 'tahun'];

    /* ---- Relationships ---- */

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }

    /* ---- Scopes ---- */

    /**
     * Scope: filter program berdasarkan tahun tertentu.
     */
    public function scopeForYear(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope: filter program berdasarkan tahun yang dipilih di session login.
     * Jika session kosong, gunakan tahun berjalan.
     */
    public function scopeForSelectedYear(Builder $query): Builder
    {
        $tahun = (int) session('selected_year', date('Y'));
        return $query->where('tahun', $tahun);
    }
}
