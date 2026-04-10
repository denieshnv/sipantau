<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Available Years
    |--------------------------------------------------------------------------
    |
    | Daftar tahun yang tersedia untuk dipilih pada halaman login.
    | Data difilter berdasarkan kolom 'tahun' di tabel programs.
    |
    */

    'available_years' => array_map('intval', array_filter(
        explode(',', env('SIPANTAU_AVAILABLE_YEARS', date('Y')))
    )),

    /*
    |--------------------------------------------------------------------------
    | Default Year
    |--------------------------------------------------------------------------
    |
    | Tahun default yang akan dipilih saat halaman login dibuka.
    |
    */

    'default_year' => (int) env('SIPANTAU_DEFAULT_YEAR', date('Y')),

];
