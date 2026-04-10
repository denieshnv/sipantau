<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = ['program_id', 'nama_kegiatan'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subkegiatans()
    {
        return $this->hasMany(Subkegiatan::class);
    }
}
