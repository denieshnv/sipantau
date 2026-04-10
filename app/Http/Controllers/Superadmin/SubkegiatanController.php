<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\Subkegiatan;
use Illuminate\Http\Request;

class SubkegiatanController extends Controller
{
    public function index()
    {
        $subkegiatans = Subkegiatan::with('kegiatan.program')
            ->whereHas('kegiatan.program', fn($q) => $q->forSelectedYear())
            ->withCount('dokumenSpjs')
            ->latest()
            ->paginate(15);
        return view('superadmin.subkegiatans.index', compact('subkegiatans'));
    }

    public function create()
    {
        $kegiatans = Kegiatan::with('program')
            ->whereHas('program', fn($q) => $q->forSelectedYear())
            ->orderBy('nama_kegiatan')
            ->get();
        return view('superadmin.subkegiatans.create', compact('kegiatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'nama_subkegiatan' => 'required|string|max:255',
        ]);

        Subkegiatan::create($request->only('kegiatan_id', 'nama_subkegiatan'));

        return redirect()->route('superadmin.subkegiatans.index')
            ->with('success', 'Subkegiatan berhasil ditambahkan.');
    }

    public function edit(Subkegiatan $subkegiatan)
    {
        $kegiatans = Kegiatan::with('program')
            ->whereHas('program', fn($q) => $q->forSelectedYear())
            ->orderBy('nama_kegiatan')
            ->get();
        return view('superadmin.subkegiatans.edit', compact('subkegiatan', 'kegiatans'));
    }

    public function update(Request $request, Subkegiatan $subkegiatan)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'nama_subkegiatan' => 'required|string|max:255',
        ]);

        $subkegiatan->update($request->only('kegiatan_id', 'nama_subkegiatan'));

        return redirect()->route('superadmin.subkegiatans.index')
            ->with('success', 'Subkegiatan berhasil diperbarui.');
    }

    public function destroy(Subkegiatan $subkegiatan)
    {
        $subkegiatan->delete();

        return redirect()->route('superadmin.subkegiatans.index')
            ->with('success', 'Subkegiatan berhasil dihapus.');
    }
}
