@extends('layouts.app')
@section('title', 'Validasi Dokumen SPJ')
@section('page-title', 'Validasi Dokumen SPJ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('superadmin.dokumen.index') }}">Dokumen SPJ</a></li>
    <li class="breadcrumb-item active">Validasi</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Detail Dokumen --}}
    <div class="col-lg-7">
        <div class="table-container">
            <div class="p-3 border-bottom" style="background: #f8fafc;">
                <h6 class="mb-0 fw-bold" style="color: #2E7D32;">
                    <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Detail Dokumen SPJ
                </h6>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-sm-4"><strong class="text-muted">Sub Kegiatan</strong></div>
                    <div class="col-sm-8">{{ $dokumen->subkegiatan->nama_subkegiatan }}</div>

                    <div class="col-sm-4"><strong class="text-muted">Kegiatan</strong></div>
                    <div class="col-sm-8">{{ $dokumen->subkegiatan->kegiatan->nama_kegiatan }}</div>

                    <div class="col-sm-4"><strong class="text-muted">Program</strong></div>
                    <div class="col-sm-8">{{ $dokumen->subkegiatan->kegiatan->program->nama_program }}</div>

                    <div class="col-12"><hr class="my-1"></div>

                    <div class="col-sm-4"><strong class="text-muted">Pengunggah</strong></div>
                    <div class="col-sm-8">
                        <i class="bi bi-person-circle text-primary me-1"></i>
                        {{ $dokumen->user->name ?? '-' }}
                    </div>

                    <div class="col-sm-4"><strong class="text-muted">Tanggal Pelaksanaan</strong></div>
                    <div class="col-sm-8">
                        <i class="bi bi-calendar-event text-primary me-1"></i>
                        {{ $dokumen->tanggal_pelaksanaan->format('d F Y') }}
                    </div>

                    <div class="col-sm-4"><strong class="text-muted">Waktu Upload</strong></div>
                    <div class="col-sm-8">
                        <i class="bi bi-clock text-muted me-1"></i>
                        {{ $dokumen->created_at->format('d F Y, H:i') }}
                    </div>

                    <div class="col-sm-4"><strong class="text-muted">File</strong></div>
                    <div class="col-sm-8">
                        <a href="{{ route('dokumen.download', $dokumen) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>Download PDF
                        </a>
                        <div class="mt-1">
                            <small class="text-muted" style="word-break: break-all;">{{ basename($dokumen->file_path) }}</small>
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    <div class="col-sm-4"><strong class="text-muted">Status Saat Ini</strong></div>
                    <div class="col-sm-8">
                        <span class="badge {{ $dokumen->status_badge }}" style="border-radius: 6px; font-size: 0.85rem; padding: 6px 14px;">
                            <i class="bi {{ $dokumen->status_icon }} me-1"></i>{{ $dokumen->status_label }}
                        </span>
                    </div>

                    @if($dokumen->validator)
                    <div class="col-sm-4"><strong class="text-muted">Divalidasi Oleh</strong></div>
                    <div class="col-sm-8">
                        {{ $dokumen->validator->name }}
                        @if($dokumen->validated_at)
                            <span class="text-muted ms-1" style="font-size: 0.8rem;">
                                ({{ $dokumen->validated_at->format('d/m/Y H:i') }})
                            </span>
                        @endif
                    </div>
                    @endif

                    @if($dokumen->catatan_validasi)
                    <div class="col-sm-4"><strong class="text-muted">Catatan</strong></div>
                    <div class="col-sm-8">
                        <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.85rem; border-radius: 8px;">
                            <i class="bi bi-chat-left-text me-1"></i>{{ $dokumen->catatan_validasi }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Form Validasi --}}
    <div class="col-lg-5">
        <div class="table-container">
            <div class="p-3 border-bottom" style="background: #f8fafc;">
                <h6 class="mb-0 fw-bold" style="color: #2E7D32;">
                    <i class="bi bi-clipboard-check-fill text-info me-2"></i>Form Validasi
                </h6>
            </div>
            <div class="p-4">
                <form action="{{ route('superadmin.dokumen.validasi.update', $dokumen) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Pilihan Status --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status Validasi <span class="text-danger">*</span></label>

                        <div class="d-flex flex-column gap-2">
                            {{-- Sudah Divalidasi --}}
                            <label class="validasi-option d-flex align-items-center p-3 rounded-3 border {{ old('status_validasi', $dokumen->status_validasi) === 'sudah_divalidasi' ? 'border-success bg-success-subtle' : '' }}"
                                   style="cursor: pointer; transition: all 0.2s ease;"
                                   id="label-sudah">
                                <input type="radio" name="status_validasi" value="sudah_divalidasi"
                                       class="form-check-input me-3"
                                       {{ old('status_validasi', $dokumen->status_validasi) === 'sudah_divalidasi' ? 'checked' : '' }}
                                       onchange="toggleCatatan(this)">
                                <div>
                                    <div class="fw-semibold text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Sudah Divalidasi
                                    </div>
                                    <small class="text-muted">Dokumen SPJ sudah sesuai dan valid.</small>
                                </div>
                            </label>

                            {{-- Perlu Perbaikan --}}
                            <label class="validasi-option d-flex align-items-center p-3 rounded-3 border {{ old('status_validasi', $dokumen->status_validasi) === 'perlu_perbaikan' ? 'border-warning bg-warning-subtle' : '' }}"
                                   style="cursor: pointer; transition: all 0.2s ease;"
                                   id="label-perbaikan">
                                <input type="radio" name="status_validasi" value="perlu_perbaikan"
                                       class="form-check-input me-3"
                                       {{ old('status_validasi', $dokumen->status_validasi) === 'perlu_perbaikan' ? 'checked' : '' }}
                                       onchange="toggleCatatan(this)">
                                <div>
                                    <div class="fw-semibold text-warning">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Perlu Perbaikan
                                    </div>
                                    <small class="text-muted">Ada yang perlu diperbaiki dalam dokumen ini.</small>
                                </div>
                            </label>
                        </div>
                        @error('status_validasi')
                            <div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Single Catatan Textarea --}}
                    <div class="mb-4" id="catatan-container">
                        <label for="catatan_validasi" class="form-label fw-semibold" id="catatan-label">
                            Catatan <span id="catatan-required" style="{{ old('status_validasi', $dokumen->status_validasi) === 'perlu_perbaikan' ? '' : 'display:none;' }}"><span class="text-danger">*</span></span>
                        </label>
                        <textarea name="catatan_validasi" id="catatan_validasi" rows="4"
                                  class="form-control @error('catatan_validasi') is-invalid @enderror"
                                  placeholder="Tuliskan catatan validasi..."
                                  style="border-radius: 8px;">{{ old('catatan_validasi', $dokumen->catatan_validasi) }}</textarea>
                        @error('catatan_validasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-1 d-block" id="catatan-hint">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="catatan-hint-text">
                                @if(old('status_validasi', $dokumen->status_validasi) === 'perlu_perbaikan')
                                    Catatan wajib diisi jika status Perlu Perbaikan.
                                @else
                                    Catatan ini akan ditampilkan ke admin yang mengunggah dokumen.
                                @endif
                            </span>
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-check2-square me-1"></i>Simpan Validasi
                        </button>
                        <a href="{{ route('superadmin.dokumen.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleCatatan(radio) {
    const labelSudah = document.getElementById('label-sudah');
    const labelPerbaikan = document.getElementById('label-perbaikan');
    const catatanRequired = document.getElementById('catatan-required');
    const catatanHintText = document.getElementById('catatan-hint-text');

    // Reset styles
    labelSudah.classList.remove('border-success', 'bg-success-subtle');
    labelPerbaikan.classList.remove('border-warning', 'bg-warning-subtle');

    if (radio.value === 'perlu_perbaikan') {
        labelPerbaikan.classList.add('border-warning', 'bg-warning-subtle');
        catatanRequired.style.display = '';
        catatanHintText.textContent = 'Catatan wajib diisi jika status Perlu Perbaikan.';
    } else {
        labelSudah.classList.add('border-success', 'bg-success-subtle');
        catatanRequired.style.display = 'none';
        catatanHintText.textContent = 'Catatan ini akan ditampilkan ke admin yang mengunggah dokumen.';
    }
}
</script>
@endpush
@endsection
