@extends('layouts.app')
@section('title', 'Data Program')
@section('page-title', 'Data Program')
@section('breadcrumb')
    <li class="breadcrumb-item">Data Master</li>
    <li class="breadcrumb-item active">Program</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0" style="font-size: 0.85rem;">Daftar program kegiatan yang terdaftar dalam sistem.</p>
    <a href="{{ route('superadmin.programs.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Program
    </a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Nama Program</th>
                    <th>Jumlah Kegiatan</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $i => $program)
                <tr>
                    <td>{{ $programs->firstItem() + $i }}</td>
                    <td class="fw-semibold">{{ $program->nama_program }}</td>
                    <td><span class="badge bg-primary-subtle text-primary" style="border-radius: 6px;">{{ $program->kegiatans_count }} kegiatan</span></td>
                    <td>
                        <a href="{{ route('superadmin.programs.edit', $program) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('superadmin.programs.destroy', $program) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus program ini? Semua kegiatan & sub kegiatan di bawahnya juga akan terhapus.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Belum ada data program.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($programs->hasPages())
    <div class="p-3 border-top">{{ $programs->links() }}</div>
    @endif
</div>
@endsection
