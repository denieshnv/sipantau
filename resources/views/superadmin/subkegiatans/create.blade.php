@extends('layouts.app')
@section('title', 'Tambah Sub Kegiatan')
@section('page-title', 'Tambah Sub Kegiatan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('superadmin.subkegiatans.index') }}">Sub Kegiatan</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="table-container p-4" style="max-width: 600px;">
    <form method="POST" action="{{ route('superadmin.subkegiatans.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Kegiatan</label>
            <select name="kegiatan_id" class="form-select @error('kegiatan_id') is-invalid @enderror" required>
                <option value="">-- Pilih Kegiatan --</option>
                @foreach($kegiatans as $kegiatan)
                    <option value="{{ $kegiatan->id }}" {{ old('kegiatan_id') == $kegiatan->id ? 'selected' : '' }}>
                        [{{ $kegiatan->program->nama_program }}] {{ $kegiatan->nama_kegiatan }}
                    </option>
                @endforeach
            </select>
            @error('kegiatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Sub Kegiatan</label>
            <input type="text" name="nama_subkegiatan" class="form-control @error('nama_subkegiatan') is-invalid @enderror" value="{{ old('nama_subkegiatan') }}" required placeholder="Masukkan nama sub kegiatan">
            @error('nama_subkegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            <a href="{{ route('superadmin.subkegiatans.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
