@extends('layouts.app')
@section('title', 'Data Sub Kegiatan')
@section('page-title', 'Data Sub Kegiatan')
@section('breadcrumb')
    <li class="breadcrumb-item">Data Master</li>
    <li class="breadcrumb-item active">Sub Kegiatan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0" style="font-size: 0.85rem;">Daftar sub kegiatan beserta jumlah dokumen SPJ.</p>
    <a href="{{ route('superadmin.subkegiatans.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Sub Kegiatan
    </a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Program</th>
                    <th>Kegiatan</th>
                    <th>Nama Sub Kegiatan</th>
                    <th>Dokumen SPJ</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subkegiatans as $i => $sub)
                <tr>
                    <td>{{ $subkegiatans->firstItem() + $i }}</td>
                    <td><span class="badge bg-primary-subtle text-primary" style="border-radius:6px;">{{ $sub->kegiatan->program->nama_program }}</span></td>
                    <td>{{ $sub->kegiatan->nama_kegiatan }}</td>
                    <td class="fw-semibold">{{ $sub->nama_subkegiatan }}</td>
                    <td>
                        <span class="badge {{ $sub->dokumen_spjs_count > 0 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="border-radius:6px;">
                            {{ $sub->dokumen_spjs_count }} dokumen
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('superadmin.subkegiatans.edit', $sub) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('superadmin.subkegiatans.destroy', $sub) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sub kegiatan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:2rem;"></i><p class="mt-2 mb-0">Belum ada data.</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subkegiatans->hasPages())<div class="p-3 border-top">{{ $subkegiatans->links() }}</div>@endif
</div>
@endsection
