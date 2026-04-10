<?php

namespace App\Http\Controllers\Camat;

use App\Http\Controllers\Controller;
use App\Models\DokumenSpj;
use App\Models\Program;
use App\Models\Subkegiatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $status = $request->input('status'); // sudah_divalidasi, belum_divalidasi, perlu_perbaikan

        $programs = Program::forSelectedYear()->with(['kegiatans.subkegiatans'])->get();

        // Ambil subkegiatan hanya dari program tahun terpilih
        $subkegiatanIds = $programs->flatMap(function ($program) {
            return $program->kegiatans->flatMap(function ($kegiatan) {
                return $kegiatan->subkegiatans->pluck('id');
            });
        });

        $subkegiatans = Subkegiatan::whereIn('id', $subkegiatanIds)
            ->with('kegiatan.program')
            ->get();

        $stats = [];
        foreach ($subkegiatans as $sub) {
            $baseQuery = DokumenSpj::where('subkegiatan_id', $sub->id);
            if ($bulan) {
                $baseQuery->whereMonth('tanggal_pelaksanaan', $bulan);
            }

            $count = (clone $baseQuery)->count();
            $sudahValidasi = (clone $baseQuery)->where('status_validasi', 'sudah_divalidasi')->count();
            $belumValidasi = (clone $baseQuery)->where('status_validasi', 'belum_divalidasi')->count();
            $perluPerbaikan = (clone $baseQuery)->where('status_validasi', 'perlu_perbaikan')->count();

            $stats[$sub->id] = [
                'subkegiatan' => $sub,
                'jumlah_dokumen' => $count,
                'program' => $sub->kegiatan->program->nama_program,
                'kegiatan' => $sub->kegiatan->nama_kegiatan,
                'sudah_validasi' => $sudahValidasi,
                'belum_validasi' => $belumValidasi,
                'perlu_perbaikan' => $perluPerbaikan,
            ];
        }

        $totalSubkegiatan = $subkegiatans->count();
        $subkegiatanDenganDokumen = collect($stats)->filter(fn($s) => $s['jumlah_dokumen'] > 0)->count();
        $persentaseGlobal = $totalSubkegiatan > 0 ? round(($subkegiatanDenganDokumen / $totalSubkegiatan) * 100, 1) : 0;

        $baseDocQuery = DokumenSpj::whereIn('subkegiatan_id', $subkegiatanIds)
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_pelaksanaan', $bulan));
        $totalDokumen = (clone $baseDocQuery)->count();
        $totalSudahValidasi = (clone $baseDocQuery)->where('status_validasi', 'sudah_divalidasi')->count();
        $totalBelumValidasi = (clone $baseDocQuery)->where('status_validasi', 'belum_divalidasi')->count();
        $totalPerluPerbaikan = (clone $baseDocQuery)->where('status_validasi', 'perlu_perbaikan')->count();

        // Filter program/kegiatan/subkegiatan berdasarkan status validasi
        // Hanya tampilkan yang punya dokumen sesuai status yang dipilih
        $isFiltered = !empty($bulan) || !empty($status);

        if ($isFiltered) {
            // Tentukan subkegiatan IDs yang relevan berdasarkan filter
            $relevantSubkegiatanIds = collect($stats)->filter(function ($s) use ($status) {
                if ($status === 'sudah_divalidasi') {
                    return $s['sudah_validasi'] > 0;
                } elseif ($status === 'belum_divalidasi') {
                    return $s['belum_validasi'] > 0;
                } elseif ($status === 'perlu_perbaikan') {
                    return $s['perlu_perbaikan'] > 0;
                }
                // Jika hanya filter bulan tanpa status, tampilkan yang punya dokumen di bulan itu
                return $s['jumlah_dokumen'] > 0;
            })->keys()->toArray();

            // Filter programs -> kegiatans -> subkegiatans supaya hanya tampil yang relevan
            $programs = $programs->map(function ($program) use ($relevantSubkegiatanIds) {
                $program->setRelation('kegiatans', $program->kegiatans->map(function ($kegiatan) use ($relevantSubkegiatanIds) {
                    $kegiatan->setRelation('subkegiatans', $kegiatan->subkegiatans->filter(function ($sub) use ($relevantSubkegiatanIds) {
                        return in_array($sub->id, $relevantSubkegiatanIds);
                    }));
                    return $kegiatan;
                })->filter(function ($kegiatan) {
                    return $kegiatan->subkegiatans->isNotEmpty();
                }));
                return $program;
            })->filter(function ($program) {
                return $program->kegiatans->isNotEmpty();
            });
        }

        return view('camat.dashboard', compact(
            'programs', 'stats', 'bulan', 'status',
            'totalSubkegiatan', 'subkegiatanDenganDokumen', 'persentaseGlobal',
            'totalDokumen',
            'totalSudahValidasi', 'totalBelumValidasi', 'totalPerluPerbaikan'
        ));
    }
}
