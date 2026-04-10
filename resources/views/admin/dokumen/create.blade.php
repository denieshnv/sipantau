@extends('layouts.app')
@section('title', 'Unggah Dokumen SPJ')
@section('page-title', 'Unggah Dokumen SPJ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pptk.dokumen.index') }}">Dokumen SPJ</a></li>
    <li class="breadcrumb-item active">Unggah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="table-container p-4">
            <h6 class="fw-bold mb-3" style="color: #2E7D32;">
                <i class="bi bi-cloud-arrow-up-fill me-1 text-primary"></i> Formulir Unggah SPJ
            </h6>

            <form method="POST" action="{{ route('pptk.dokumen.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Dropdown Program --}}
                <div class="mb-3">
                    <label class="form-label">Program <span class="text-danger">*</span></label>
                    <select id="select-program" class="form-select @error('program_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->nama_program }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Dropdown Kegiatan --}}
                <div class="mb-3">
                    <label class="form-label">Kegiatan <span class="text-danger">*</span></label>
                    <select id="select-kegiatan" class="form-select" disabled required>
                        <option value="">-- Pilih Kegiatan --</option>
                    </select>
                    <div class="form-text" id="kegiatan-hint">
                        <i class="bi bi-info-circle"></i> Pilih program terlebih dahulu.
                    </div>
                </div>

                {{-- Dropdown Sub Kegiatan --}}
                <div class="mb-3">
                    <label class="form-label">Sub Kegiatan <span class="text-danger">*</span></label>
                    <select name="subkegiatan_id" id="select-subkegiatan" class="form-select @error('subkegiatan_id') is-invalid @enderror" disabled required>
                        <option value="">-- Pilih Sub Kegiatan --</option>
                    </select>
                    @error('subkegiatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text" id="subkegiatan-hint">
                        <i class="bi bi-info-circle"></i> Pilih kegiatan terlebih dahulu.
                    </div>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_pelaksanaan" class="form-control @error('tanggal_pelaksanaan') is-invalid @enderror"
                           value="{{ old('tanggal_pelaksanaan') }}" required id="tanggal-pelaksanaan">
                    @error('tanggal_pelaksanaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">File Dokumen SPJ <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                           accept=".pdf" required id="file-input">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">
                        <i class="bi bi-info-circle"></i>
                        Hanya file PDF, ukuran maksimal 5MB.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="upload-submit">
                    <i class="bi bi-cloud-arrow-up"></i> Unggah Dokumen
                </button>
                <a href="{{ route('pptk.dokumen.index') }}" class="btn btn-outline-secondary ms-1">Batal</a>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="table-container p-4">
            <h6 class="fw-bold mb-3" style="color: #2E7D32;">
                <i class="bi bi-info-circle me-1"></i> Panduan Unggah
            </h6>
            <ul class="list-unstyled" style="font-size: 0.85rem; color: #475569;">
                <li class="mb-2">
                    <i class="bi bi-1-circle-fill text-primary me-2"></i>
                    Pilih <strong>Program</strong> terlebih dahulu.
                </li>
                <li class="mb-2">
                    <i class="bi bi-2-circle-fill text-primary me-2"></i>
                    Pilih <strong>Kegiatan</strong> sesuai program.
                </li>
                <li class="mb-2">
                    <i class="bi bi-3-circle-fill text-primary me-2"></i>
                    Pilih <strong>Sub Kegiatan</strong> yang relevan.
                </li>
                <li class="mb-2">
                    <i class="bi bi-4-circle-fill text-primary me-2"></i>
                    Isi <strong>Tanggal Pelaksanaan</strong> kegiatan.
                </li>
                <li class="mb-2">
                    <i class="bi bi-5-circle-fill text-primary me-2"></i>
                    Unggah scan dokumen SPJ (SPPD, laporan, bukti belanja).
                </li>
                <li class="mb-2">
                    <i class="bi bi-exclamation-circle-fill text-warning me-2"></i>
                    Hanya file <strong>PDF</strong> yang diterima.
                </li>
                <li class="mb-2">
                    <i class="bi bi-exclamation-circle-fill text-warning me-2"></i>
                    Ukuran file maksimal <strong>5MB</strong>.
                </li>
                <li>
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                    Nama file otomatis diformat oleh sistem.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const selectProgram = document.getElementById('select-program');
    const selectKegiatan = document.getElementById('select-kegiatan');
    const selectSubkegiatan = document.getElementById('select-subkegiatan');
    const kegiatanHint = document.getElementById('kegiatan-hint');
    const subkegiatanHint = document.getElementById('subkegiatan-hint');

    selectProgram.addEventListener('change', function() {
        const programId = this.value;

        // Reset kegiatan & subkegiatan
        selectKegiatan.innerHTML = '<option value="">-- Pilih Kegiatan --</option>';
        selectKegiatan.disabled = true;
        selectSubkegiatan.innerHTML = '<option value="">-- Pilih Sub Kegiatan --</option>';
        selectSubkegiatan.disabled = true;
        kegiatanHint.innerHTML = '<i class="bi bi-hourglass-split"></i> Memuat data kegiatan...';
        subkegiatanHint.innerHTML = '<i class="bi bi-info-circle"></i> Pilih kegiatan terlebih dahulu.';

        if (!programId) {
            kegiatanHint.innerHTML = '<i class="bi bi-info-circle"></i> Pilih program terlebih dahulu.';
            return;
        }

        fetch(`/api/kegiatans/${programId}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    kegiatanHint.innerHTML = '<i class="bi bi-exclamation-circle text-warning"></i> Tidak ada kegiatan untuk program ini.';
                    return;
                }
                data.forEach(k => {
                    const opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = k.nama_kegiatan;
                    selectKegiatan.appendChild(opt);
                });
                selectKegiatan.disabled = false;
                kegiatanHint.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${data.length} kegiatan tersedia.`;
            })
            .catch(() => {
                kegiatanHint.innerHTML = '<i class="bi bi-x-circle text-danger"></i> Gagal memuat data.';
            });
    });

    selectKegiatan.addEventListener('change', function() {
        const kegiatanId = this.value;

        // Reset subkegiatan
        selectSubkegiatan.innerHTML = '<option value="">-- Pilih Sub Kegiatan --</option>';
        selectSubkegiatan.disabled = true;
        subkegiatanHint.innerHTML = '<i class="bi bi-hourglass-split"></i> Memuat data sub kegiatan...';

        if (!kegiatanId) {
            subkegiatanHint.innerHTML = '<i class="bi bi-info-circle"></i> Pilih kegiatan terlebih dahulu.';
            return;
        }

        fetch(`/api/subkegiatans/${kegiatanId}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    subkegiatanHint.innerHTML = '<i class="bi bi-exclamation-circle text-warning"></i> Tidak ada sub kegiatan untuk kegiatan ini.';
                    return;
                }
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.nama_subkegiatan;
                    selectSubkegiatan.appendChild(opt);
                });
                selectSubkegiatan.disabled = false;
                subkegiatanHint.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${data.length} sub kegiatan tersedia.`;
            })
            .catch(() => {
                subkegiatanHint.innerHTML = '<i class="bi bi-x-circle text-danger"></i> Gagal memuat data.';
            });
    });
</script>
@endpush
