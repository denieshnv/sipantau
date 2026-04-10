@extends('layouts.app')
@section('title', 'Kelola Tahun Anggaran')
@section('page-title', 'Kelola Tahun Anggaran')
@section('breadcrumb')
    <li class="breadcrumb-item active">Tahun Anggaran</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Form Tambah Tahun --}}
    <div class="col-lg-5">
        <div class="table-container p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-calendar-plus me-2"></i>Tambah Tahun Baru</h6>
            <form action="{{ route('superadmin.tahun.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="tahun" class="form-label">Tahun Anggaran</label>
                    <input type="number" name="tahun" id="tahun" class="form-control"
                           min="2020" max="2099" value="{{ date('Y') + 1 }}" required>
                    <div class="form-text">Masukkan tahun baru yang akan ditambahkan ke sistem.</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Tahun
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Tahun --}}
    <div class="col-lg-7">
        <div class="table-container">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2"></i>Daftar Tahun Tersedia</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Tahun</th>
                            <th>Jumlah Program</th>
                            <th>Status</th>
                            <th style="width: 200px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availableYears as $i => $year)
                        @php
                            $programCount = \App\Models\Program::where('tahun', $year)->count();
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-bold" style="font-size: 1.1rem;">{{ $year }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary" style="font-size: 0.8rem;">
                                    {{ $programCount }} Program
                                </span>
                            </td>
                            <td>
                                @if($year == $defaultYear)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-star-fill me-1"></i>Default
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($year != $defaultYear)
                                    <form action="{{ route('superadmin.tahun.default') }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="tahun" value="{{ $year }}">
                                        <button class="btn btn-sm btn-outline-success" title="Jadikan Default">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($programCount === 0 && count($availableYears) > 1)
                                    <form action="{{ route('superadmin.tahun.destroy') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus tahun {{ $year }} dari daftar?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="tahun" value="{{ $year }}">
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-info mt-3" style="border-radius: 10px; font-size: 0.85rem;">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Info:</strong> Tahun yang sudah memiliki data Program tidak bisa dihapus.
            Tahun bertanda <span class="badge bg-success-subtle text-success">Default</span> akan otomatis terpilih saat login.
        </div>
    </div>
</div>
@endsection
