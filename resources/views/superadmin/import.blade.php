@extends('layouts.app')
@section('title', 'Import Data')
@section('page-title', 'Import Data Master')
@section('breadcrumb')
    <li class="breadcrumb-item active">Import Data</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="table-container p-4">
            <h6 class="fw-bold mb-3" style="color: #2E7D32;">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Import dari File CSV
            </h6>
            <p class="text-muted" style="font-size: 0.82rem;">
                Import data Program, Kegiatan, dan Sub Kegiatan secara massal dari file CSV.
                Data yang sudah ada tidak akan diduplikasi.
            </p>

            <div class="alert alert-info py-2" style="border-radius: 10px; font-size: 0.82rem;">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Format CSV:</strong> File harus memiliki 3 kolom dengan header:
                <code>program, kegiatan, subkegiatan</code>
            </div>

            <form method="POST" action="{{ route('superadmin.import.process') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Pilih File</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                           accept=".csv,.txt,.xlsx,.xls" required>
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Format yang didukung: CSV (.csv)</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Import Data
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="table-container p-4">
            <h6 class="fw-bold mb-3" style="color: #2E7D32;">
                <i class="bi bi-file-earmark-text me-1"></i> Contoh Format CSV
            </h6>
            <div class="bg-dark text-light p-3 rounded" style="font-size: 0.82rem; font-family: monospace;">
                <div style="color: #66BB6A;">program,kegiatan,subkegiatan</div>
                <div>Program Penunjang,Administrasi Umum,Penyediaan ATK</div>
                <div>Program Penunjang,Administrasi Umum,Penyediaan Makan Minum</div>
                <div>Program Penunjang,Perjalanan Dinas,SPPD Dalam Daerah</div>
                <div>Program Pembangunan,Infrastruktur,Belanja Modal Gedung</div>
            </div>
            <p class="text-muted mt-2" style="font-size: 0.78rem;">
                <i class="bi bi-lightbulb me-1"></i>
                Setiap baris akan otomatis membuat hierarki Program → Kegiatan → Sub Kegiatan.
                Data duplikat akan diabaikan.
            </p>
        </div>
    </div>
</div>
@endsection
