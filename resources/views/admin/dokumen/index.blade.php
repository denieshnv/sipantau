@extends('layouts.app')
@section('title', 'Riwayat Dokumen SPJ')
@section('page-title', auth()->user()->canViewAllDocuments() ? 'Semua Dokumen SPJ' : 'Dokumen SPJ Saya')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dokumen SPJ</li>
@endsection

@section('content')
@if(auth()->user()->canUploadSpj())
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0" style="font-size: 0.85rem;">Riwayat dokumen SPJ yang telah Anda unggah.</p>
    <a href="{{ route('pptk.dokumen.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-cloud-arrow-up"></i> Unggah SPJ Baru
    </a>
</div>
@else
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0" style="font-size: 0.85rem;">Seluruh dokumen SPJ yang diunggah oleh PPTK.</p>
    @if(auth()->user()->canDownloadAll())
    <a href="{{ route('dokumen.download-all') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-file-earmark-zip"></i> Download Semua (ZIP)
    </a>
    @endif
</div>
@endif

{{-- Filter Status Validasi --}}
<div class="table-container p-3 mb-3">
    <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
        <label class="form-label mb-0 fw-semibold" style="white-space: nowrap;">
            <i class="bi bi-funnel me-1"></i>Filter Status:
        </label>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="border-radius: 20px;">
                Semua
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'belum_divalidasi']) }}"
               class="btn btn-sm {{ request('status') === 'belum_divalidasi' ? 'btn-secondary' : 'btn-outline-secondary' }}"
               style="border-radius: 20px;">
                <i class="bi bi-hourglass-split"></i> Belum Divalidasi
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'sudah_divalidasi']) }}"
               class="btn btn-sm {{ request('status') === 'sudah_divalidasi' ? 'btn-success' : 'btn-outline-success' }}"
               style="border-radius: 20px;">
                <i class="bi bi-check-circle"></i> Sudah Divalidasi
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'perlu_perbaikan']) }}"
               class="btn btn-sm {{ request('status') === 'perlu_perbaikan' ? 'btn-warning' : 'btn-outline-warning' }}"
               style="border-radius: 20px;">
                <i class="bi bi-exclamation-triangle"></i> Perlu Perbaikan
            </a>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    @if(auth()->user()->canViewAllDocuments())
                    <th>Pengunggah</th>
                    @endif
                    <th>Sub Kegiatan</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Waktu Pengumpulan</th>
                    <th>Status Validasi</th>
                    <th>File</th>
                    <th style="width: 160px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dokumens as $i => $dokumen)
                <tr>
                    <td>{{ $dokumens->firstItem() + $i }}</td>
                    @if(auth()->user()->canViewAllDocuments())
                    <td>
                        <span class="fw-semibold">{{ $dokumen->user->name ?? '-' }}</span>
                    </td>
                    @endif
                    <td>
                        <div class="fw-semibold">{{ $dokumen->subkegiatan->nama_subkegiatan }}</div>
                        <small class="text-muted">{{ $dokumen->subkegiatan->kegiatan->program->nama_program }} &raquo; {{ $dokumen->subkegiatan->kegiatan->nama_kegiatan }}</small>
                    </td>
                    <td>
                        <i class="bi bi-calendar-event text-primary me-1"></i>
                        {{ $dokumen->tanggal_pelaksanaan->format('d/m/Y') }}
                    </td>
                    <td>
                        <i class="bi bi-clock text-muted me-1"></i>
                        <span style="font-size: 0.82rem;">{{ $dokumen->created_at->format('d/m/Y H:i') }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $dokumen->status_badge }}" style="border-radius: 6px; font-size: 0.78rem; padding: 5px 10px;">
                            <i class="bi {{ $dokumen->status_icon }} me-1"></i>{{ $dokumen->status_label }}
                        </span>
                        @if($dokumen->isPerluPerbaikan() && $dokumen->catatan_validasi)
                            <div class="mt-1">
                                <small class="text-warning" style="font-size: 0.75rem;" data-bs-toggle="tooltip" title="{{ $dokumen->catatan_validasi }}">
                                    <i class="bi bi-chat-left-text me-1"></i>Ada catatan
                                </small>
                            </div>
                        @endif
                        @if($dokumen->validator && $dokumen->validated_at)
                            <div class="mt-1">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    oleh {{ $dokumen->validator->name }} · {{ $dokumen->validated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center" style="max-width: 180px;">
                            <i class="bi bi-file-earmark-pdf text-danger me-1 flex-shrink-0"></i>
                            <span style="font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ basename($dokumen->file_path) }}">{{ basename($dokumen->file_path) }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('dokumen.download', $dokumen) }}" class="btn btn-sm btn-outline-primary" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            @if(auth()->user()->canValidate())
                            <a href="{{ route('superadmin.dokumen.validasi', $dokumen) }}" class="btn btn-sm btn-outline-info" title="Validasi">
                                <i class="bi bi-clipboard-check"></i>
                            </a>
                            @endif
                            {{-- Tombol Perbaiki: hanya muncul untuk PPTK & status perlu_perbaikan --}}
                            @if(auth()->user()->isPptk() && $dokumen->isPerluPerbaikan() && $dokumen->user_id === auth()->id())
                            <button type="button" class="btn btn-sm btn-outline-warning" title="Perbaiki SPJ"
                                    onclick="openPerbaikiModal({{ $dokumen->id }}, '{{ addslashes($dokumen->subkegiatan->nama_subkegiatan) }}', '{{ addslashes($dokumen->catatan_validasi ?? '') }}')">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            @endif
                            @if(auth()->user()->canUploadSpj() && $dokumen->user_id === auth()->id())
                            <form action="{{ route('pptk.dokumen.destroy', $dokumen) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->canViewAllDocuments() ? 8 : 7 }}" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Belum ada dokumen SPJ yang diunggah.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($dokumens->hasPages())<div class="p-3 border-top">{{ $dokumens->links() }}</div>@endif
</div>

{{-- Modal Perbaiki SPJ --}}
<div class="modal fade" id="modalPerbaiki" tabindex="-1" aria-labelledby="modalPerbaikiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;">
                <h6 class="modal-title text-white fw-bold" id="modalPerbaikiLabel">
                    <i class="bi bi-arrow-repeat me-2"></i>Perbaiki Dokumen SPJ
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPerbaiki" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    {{-- Info Sub Kegiatan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted" style="font-size: 0.8rem;">SUB KEGIATAN</label>
                        <div class="fw-semibold" id="perbaiki-subkegiatan" style="color: #2E7D32;"></div>
                    </div>

                    {{-- Catatan dari Validator --}}
                    <div class="mb-3" id="perbaiki-catatan-container">
                        <label class="form-label fw-semibold text-muted" style="font-size: 0.8rem;">CATATAN DARI KASUBAG</label>
                        <div class="alert alert-warning py-2 px-3 mb-0" style="border-radius: 8px; font-size: 0.85rem;">
                            <i class="bi bi-chat-left-text me-1"></i>
                            <span id="perbaiki-catatan"></span>
                        </div>
                    </div>

                    <hr>

                    {{-- Upload File Baru --}}
                    <div class="mb-3">
                        <label for="perbaiki-file" class="form-label fw-semibold">
                            Unggah File SPJ yang Sudah Diperbaiki <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file" id="perbaiki-file" class="form-control" accept=".pdf" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            File lama akan diganti. Hanya file PDF, maks 5MB.
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3" style="border-radius: 8px; font-size: 0.82rem;">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Setelah diunggah ulang, status akan kembali ke <strong>"Belum Divalidasi"</strong> untuk ditinjau kembali oleh Kasubag.
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-semibold">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Unggah Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tooltip & Modal Scripts --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
});

function openPerbaikiModal(dokumenId, subkegiatan, catatan) {
    // Set form action
    document.getElementById('formPerbaiki').action = '/pptk/dokumen/' + dokumenId + '/perbaiki';

    // Set sub kegiatan name
    document.getElementById('perbaiki-subkegiatan').textContent = subkegiatan;

    // Set catatan
    var catatanContainer = document.getElementById('perbaiki-catatan-container');
    if (catatan && catatan.trim() !== '') {
        catatanContainer.style.display = '';
        document.getElementById('perbaiki-catatan').textContent = catatan;
    } else {
        catatanContainer.style.display = 'none';
    }

    // Reset file input
    document.getElementById('perbaiki-file').value = '';

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('modalPerbaiki'));
    modal.show();
}
</script>
@endpush
@endsection
