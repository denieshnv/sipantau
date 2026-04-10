<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subkegiatan extends Model
{
    use HasFactory;

    protected $fillable = ['kegiatan_id', 'nama_subkegiatan'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function dokumenSpjs()
    {
        return $this->hasMany(DokumenSpj::class);
    }
}
