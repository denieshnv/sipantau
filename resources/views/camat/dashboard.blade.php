@extends('layouts.app')

@section('title', 'Dashboard Progress SPJ')
@section('page-title', 'Dashboard Progress SPJ')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
{{-- Filter Bar --}}
<div class="table-container p-3 mb-4">
    <form method="GET" action="{{ route('camat.dashboard') }}" class="row g-3 align-items-end">
        <div class="col-auto">
            <label class="form-label">Bulan Pelaksanaan</label>
            <select name="bulan" class="form-select" style="min-width: 200px;">
                <option value="">Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $namaBulan)
                    <option value="{{ $i + 1 }}" {{ $bulan == ($i + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label">Status Validasi</label>
            <select name="status" class="form-select" style="min-width: 200px;">
                <option value="">Semua Status</option>
                <option value="sudah_divalidasi" {{ ($status ?? '') == 'sudah_divalidasi' ? 'selected' : '' }}>✅ Sudah Divalidasi</option>
                <option value="belum_divalidasi" {{ ($status ?? '') == 'belum_divalidasi' ? 'selected' : '' }}>⏳ Belum Divalidasi</option>
                <option value="perlu_perbaikan" {{ ($status ?? '') == 'perlu_perbaikan' ? 'selected' : '' }}>⚠️ Perlu Perbaikan</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
        @if(!empty($bulan) || !empty($status))
        <div class="col-auto">
            <a href="{{ route('camat.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Reset Filter
            </a>
        </div>
        @endif
        <div class="col-auto ms-auto">
            <a href="{{ route('dokumen.download-all') }}" class="btn btn-accent">
                <i class="bi bi-file-earmark-zip"></i> Download Semua (ZIP)
            </a>
        </div>
    </form>
</div>

{{-- Active Filter Indicator --}}
@if(!empty($bulan) || !empty($status))
<div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="border-radius: 10px; background: rgba(13,110,253,0.08); border: 1px solid rgba(13,110,253,0.2); color: #0d6efd;">
    <i class="bi bi-funnel-fill"></i>
    <span>
        <strong>Filter aktif:</strong>
        @if(!empty($bulan))
            Bulan {{ ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$bulan - 1] }}
        @endif
        @if(!empty($bulan) && !empty($status))
            &bull;
        @endif
        @if(!empty($status))
            Status: {{ $status == 'sudah_divalidasi' ? 'Sudah Divalidasi' : ($status == 'belum_divalidasi' ? 'Belum Divalidasi' : 'Perlu Perbaikan') }}
        @endif
        — Hanya menampilkan program/kegiatan/sub kegiatan yang sesuai.
    </span>
</div>
@endif

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background: rgba(46,125,50,0.1); color: #2E7D32;">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $totalDokumen }}</div>
            <div class="stat-label">Total Dokumen SPJ</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background: rgba(5,150,105,0.1); color: #059669;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $subkegiatanDenganDokumen }}/{{ $totalSubkegiatan }}</div>
            <div class="stat-label">Sub Kegiatan Terlengkapi</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background: rgba(232,185,49,0.15); color: #2E7D32;">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
            <div class="stat-value">{{ $persentaseGlobal }}%</div>
            <div class="stat-label">Kelengkapan Global</div>
            <div class="progress mt-2">
                <div class="progress-bar bg-warning" style="width: {{ $persentaseGlobal }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background: rgba(5,150,105,0.1); color: #059669;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $totalSudahValidasi }}</div>
            <div class="stat-label">Sudah Divalidasi</div>
        </div>
    </div>
</div>

{{-- Validation Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #059669;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(5,150,105,0.1); color: #059669;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ $totalSudahValidasi }}</div>
                    <div class="stat-label">Sudah Divalidasi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #6b7280;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(107,114,128,0.1); color: #6b7280;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ $totalBelumValidasi }}</div>
                    <div class="stat-label">Belum Divalidasi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ $totalPerluPerbaikan }}</div>
                    <div class="stat-label">Perlu Perbaikan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Progress Per Program (Accordion) --}}
<div class="accordion" id="accordionPrograms">
@foreach($programs as $pIndex => $program)
    @php
        $programDokumen = 0;
        $programSubTotal = 0;
        foreach($program->kegiatans as $k) {
            foreach($k->subkegiatans as $s) {
                $programDokumen += $stats[$s->id]['jumlah_dokumen'] ?? 0;
                $programSubTotal++;
            }
        }
    @endphp
    <div class="table-container mb-3" style="overflow: hidden;">
        <div class="p-3 d-flex align-items-center justify-content-between"
             style="background: #f8fafc; cursor: pointer; user-select: none;"
             data-bs-toggle="collapse" data-bs-target="#program-{{ $program->id }}"
             aria-expanded="{{ $pIndex === 0 ? 'true' : 'false' }}">
            <h6 class="mb-0 fw-bold" style="color: #2E7D32;">
                <i class="bi bi-folder-fill text-warning me-2"></i>{{ $program->nama_program }}
            </h6>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark" style="font-size: 0.75rem; border: 1px solid #e2e8f0;">
                    {{ $programSubTotal }} sub kegiatan &bull; {{ $programDokumen }} dokumen
                </span>
                <i class="bi bi-chevron-down text-muted transition-transform" id="chevron-program-{{ $program->id }}"
                   style="transition: transform 0.3s; {{ $pIndex === 0 ? 'transform: rotate(180deg);' : '' }}"></i>
            </div>
        </div>

        <div class="collapse {{ $pIndex === 0 ? 'show' : '' }}" id="program-{{ $program->id }}">

            <div class="accordion" id="accordionKegiatan-{{ $program->id }}">
            @foreach($program->kegiatans as $kIndex => $kegiatan)
                @php
                    $kegDokumen = 0;
                    $kegSubTotal = $kegiatan->subkegiatans->count();
                    foreach($kegiatan->subkegiatans as $s) {
                        $kegDokumen += $stats[$s->id]['jumlah_dokumen'] ?? 0;
                    }
                @endphp
                <div class="border-top">
                    <div class="px-3 py-2 d-flex align-items-center justify-content-between"
                         style="background: linear-gradient(90deg, #e8f5e9 0%, #f1f8f2 40%, #fff 100%); cursor: pointer; user-select: none; border-left: 3px solid #43A047;"
                         data-bs-toggle="collapse"
                         data-bs-target="#kegiatan-{{ $program->id }}-{{ $kegiatan->id }}"
                         aria-expanded="{{ ($pIndex === 0 && $kIndex === 0) ? 'true' : 'false' }}">
                        <h6 class="fw-semibold mb-0" style="font-size: 0.84rem; color: #1B5E20;">
                            <i class="bi bi-journal-text me-1" style="color: #43A047;"></i>{{ $kegiatan->nama_kegiatan }}
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="font-size: 0.7rem; background: rgba(46,125,50,0.1); color: #2E7D32; border: 1px solid rgba(46,125,50,0.2);">
                                {{ $kegSubTotal }} sub &bull; {{ $kegDokumen }} dok
                            </span>
                            <i class="bi bi-chevron-down" style="color: #43A047; font-size: 0.75rem; transition: transform 0.3s; {{ ($pIndex === 0 && $kIndex === 0) ? 'transform: rotate(180deg);' : '' }}"></i>
                        </div>
                    </div>

                    <div class="collapse {{ ($pIndex === 0 && $kIndex === 0) ? 'show' : '' }}"
                         id="kegiatan-{{ $program->id }}-{{ $kegiatan->id }}">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40%">Sub Kegiatan</th>
                                        <th>Jumlah Dokumen</th>
                                        <th>Status Validasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kegiatan->subkegiatans as $sub)
                                        @php
                                            $jumlah = $stats[$sub->id]['jumlah_dokumen'] ?? 0;
                                            $sudah = $stats[$sub->id]['sudah_validasi'] ?? 0;
                                            $belum = $stats[$sub->id]['belum_validasi'] ?? 0;
                                            $perbaikan = $stats[$sub->id]['perlu_perbaikan'] ?? 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $sub->nama_subkegiatan }}</td>
                                            <td>
                                                <span class="fw-bold" style="color: {{ $jumlah > 0 ? '#059669' : '#dc2626' }}">
                                                    {{ $jumlah }} dokumen
                                                </span>
                                            </td>
                                            <td>
                                                @if($jumlah > 0)
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        @if($sudah > 0)
                                                            <span class="badge bg-success-subtle text-success" style="border-radius: 6px; font-size: 0.72rem;">
                                                                <i class="bi bi-check-circle-fill"></i> {{ $sudah }} valid
                                                            </span>
                                                        @endif
                                                        @if($belum > 0)
                                                            <span class="badge bg-secondary-subtle text-secondary" style="border-radius: 6px; font-size: 0.72rem;">
                                                                <i class="bi bi-hourglass-split"></i> {{ $belum }} menunggu
                                                            </span>
                                                        @endif
                                                        @if($perbaikan > 0)
                                                            <span class="badge bg-warning-subtle text-warning" style="border-radius: 6px; font-size: 0.72rem;">
                                                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $perbaikan }} revisi
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Belum ada sub kegiatan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
@endforeach
</div>

@if($programs->isEmpty())
<div class="table-container p-5 text-center">
    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
    @if(!empty($bulan) || !empty($status))
        <p class="text-muted mt-2">Tidak ada program/kegiatan/sub kegiatan yang sesuai dengan filter yang dipilih.</p>
        <a href="{{ route('camat.dashboard') }}" class="btn btn-outline-primary btn-sm mt-2">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </a>
    @else
        <p class="text-muted mt-2">Belum ada data program untuk tahun ini.</p>
    @endif
</div>
@endif

@push('scripts')
<script>
    // Rotate chevron icons on collapse toggle
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(trigger) {
        const targetId = trigger.getAttribute('data-bs-target');
        const collapseEl = document.querySelector(targetId);
        if (!collapseEl) return;

        collapseEl.addEventListener('show.bs.collapse', function() {
            const chevron = trigger.querySelector('.bi-chevron-down');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        });
        collapseEl.addEventListener('hide.bs.collapse', function() {
            const chevron = trigger.querySelector('.bi-chevron-down');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        });
    });
</script>
@endpush
@endsection


