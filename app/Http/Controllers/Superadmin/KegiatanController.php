<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Program;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::with('program')
            ->whereHas('program', fn($q) => $q->forSelectedYear())
            ->withCount('subkegiatans')
            ->latest()
            ->paginate(15);
        return view('superadmin.kegiatans.index', compact('kegiatans'));
    }

    public function create()
    {
        $programs = Program::forSelectedYear()->orderBy('nama_program')->get();
        return view('superadmin.kegiatans.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        Kegiatan::create($request->only('program_id', 'nama_kegiatan'));

        return redirect()->route('superadmin.kegiatans.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan)
    {
        $programs = Program::forSelectedYear()->orderBy('nama_program')->get();
        return view('superadmin.kegiatans.edit', compact('kegiatan', 'programs'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        $kegiatan->update($request->only('program_id', 'nama_kegiatan'));

        return redirect()->route('superadmin.kegiatans.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();

        return redirect()->route('superadmin.kegiatans.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }
}
