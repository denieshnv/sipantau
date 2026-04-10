<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::forSelectedYear()->withCount('kegiatans')->latest()->paginate(15);
        return view('superadmin.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('superadmin.programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
        ]);

        Program::create([
            'nama_program' => $request->nama_program,
            'tahun' => (int) session('selected_year', date('Y')),
        ]);

        return redirect()->route('superadmin.programs.index')
            ->with('success', 'Program berhasil ditambahkan.');
    }

    public function edit(Program $program)
    {
        return view('superadmin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
        ]);

        $program->update($request->only('nama_program'));

        return redirect()->route('superadmin.programs.index')
            ->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('superadmin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }
}
