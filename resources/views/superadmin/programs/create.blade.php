@extends('layouts.app')
@section('title', 'Tambah Program')
@section('page-title', 'Tambah Program')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('superadmin.programs.index') }}">Program</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="table-container p-4" style="max-width: 600px;">
    <form method="POST" action="{{ route('superadmin.programs.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Program</label>
            <input type="text" name="nama_program" class="form-control @error('nama_program') is-invalid @enderror"
                   value="{{ old('nama_program') }}" required placeholder="Masukkan nama program">
            @error('nama_program')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            <a href="{{ route('superadmin.programs.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
