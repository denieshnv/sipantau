@extends('layouts.app')
@section('title', 'Data Kegiatan')
@section('page-title', 'Data Kegiatan')
@section('breadcrumb')
    <li class="breadcrumb-item">Data Master</li>
    <li class="breadcrumb-item active">Kegiatan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0" style="font-size: 0.85rem;">Daftar kegiatan berdasarkan program.</p>
    <a href="{{ route('superadmin.kegiatans.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Kegiatan
    </a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Program</th>
                    <th>Nama Kegiatan</th>
                    <th>Jumlah Sub Kegiatan</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatans as $i => $kegiatan)
                <tr>
                    <td>{{ $kegiatans->firstItem() + $i }}</td>
                    <td><span class="badge bg-primary-subtle text-primary" style="border-radius:6px;">{{ $kegiatan->program->nama_program }}</span></td>
                    <td class="fw-semibold">{{ $kegiatan->nama_kegiatan }}</td>
                    <td>{{ $kegiatan->subkegiatans_count }} sub kegiatan</td>
                    <td>
                        <a href="{{ route('superadmin.kegiatans.edit', $kegiatan) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('superadmin.kegiatans.destroy', $kegiatan) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kegiatan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:2rem;"></i><p class="mt-2 mb-0">Belum ada data.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kegiatans->hasPages())<div class="p-3 border-top">{{ $kegiatans->links() }}</div>@endif
</div>
@endsection
