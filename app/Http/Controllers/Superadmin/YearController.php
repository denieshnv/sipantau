<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function index()
    {
        $availableYears = config('sipantau.available_years', []);
        $defaultYear = config('sipantau.default_year', date('Y'));

        sort($availableYears);

        return view('superadmin.tahun.index', compact('availableYears', 'defaultYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
        ], [
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun minimal 2020.',
            'tahun.max' => 'Tahun maksimal 2099.',
        ]);

        $tahun = (int) $request->tahun;
        $availableYears = config('sipantau.available_years', []);

        if (in_array($tahun, $availableYears)) {
            return back()->with('error', "Tahun {$tahun} sudah ada dalam daftar.");
        }

        // Tambahkan tahun baru
        $availableYears[] = $tahun;
        sort($availableYears);

        // Update .env file
        $this->updateEnvValue('SIPANTAU_AVAILABLE_YEARS', implode(',', $availableYears));

        // Refresh config cache
        if (function_exists('config_clear')) {
            \Artisan::call('config:clear');
        }

        return redirect()->route('superadmin.tahun.index')
            ->with('success', "Tahun {$tahun} berhasil ditambahkan.");
    }

    public function setDefault(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
        ]);

        $tahun = (int) $request->tahun;
        $availableYears = config('sipantau.available_years', []);

        if (!in_array($tahun, $availableYears)) {
            return back()->with('error', "Tahun {$tahun} tidak ditemukan dalam daftar.");
        }

        $this->updateEnvValue('SIPANTAU_DEFAULT_YEAR', (string) $tahun);

        return redirect()->route('superadmin.tahun.index')
            ->with('success', "Tahun default berhasil diubah menjadi {$tahun}.");
    }

    public function destroy(Request $request)
    {
        $tahun = (int) $request->tahun;
        $availableYears = config('sipantau.available_years', []);

        if (!in_array($tahun, $availableYears)) {
            return back()->with('error', "Tahun {$tahun} tidak ditemukan.");
        }

        if (count($availableYears) <= 1) {
            return back()->with('error', 'Minimal harus ada 1 tahun yang tersedia.');
        }

        // Cek apakah ada data di tahun ini
        $programCount = \App\Models\Program::where('tahun', $tahun)->count();
        if ($programCount > 0) {
            return back()->with('error', "Tahun {$tahun} masih memiliki {$programCount} program. Hapus data terlebih dahulu.");
        }

        // Hapus dari daftar
        $availableYears = array_values(array_diff($availableYears, [$tahun]));
        $this->updateEnvValue('SIPANTAU_AVAILABLE_YEARS', implode(',', $availableYears));

        // Jika tahun yang dihapus adalah default, ubah default ke tahun pertama
        $defaultYear = config('sipantau.default_year');
        if ($defaultYear == $tahun) {
            $this->updateEnvValue('SIPANTAU_DEFAULT_YEAR', (string) $availableYears[0]);
        }

        return redirect()->route('superadmin.tahun.index')
            ->with('success', "Tahun {$tahun} berhasil dihapus dari daftar.");
    }

    /**
     * Update nilai pada file .env
     */
    private function updateEnvValue(string $key, string $value): void
    {
        $envFile = base_path('.env');
        $content = file_get_contents($envFile);

        if (str_contains($content, "{$key}=")) {
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $content
            );
        } else {
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($envFile, $content);
    }
}
