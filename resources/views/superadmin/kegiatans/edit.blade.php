@extends('layouts.app')
@section('title', 'Edit Kegiatan')
@section('page-title', 'Edit Kegiatan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('superadmin.kegiatans.index') }}">Kegiatan</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="table-container p-4" style="max-width: 600px;">
    <form method="POST" action="{{ route('superadmin.kegiatans.update', $kegiatan) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Program</label>
            <select name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ old('program_id', $kegiatan->program_id) == $program->id ? 'selected' : '' }}>{{ $program->nama_program }}</option>
                @endforeach
            </select>
            @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
            @error('nama_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            <a href="{{ route('superadmin.kegiatans.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
