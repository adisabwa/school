<?php

if (! function_exists('getLabel')) {
    /**
     * Mendapatkan label lengkap berdasarkan jenis ujian.
     *
     * @param string|null $ujian
     * @return string
     */
    function getLabel(?string $ujian): string
    {
        return match ($ujian) {
            'nilai_harian'     => 'Nilai Harian',
            'uts'              => 'UTS',
            'uas'              => 'UAS',
            'um'               => 'Ujian Madrasah',
            'nilai_rapor'      => 'Nilai Raport',
            'nilai_pengasuhan' => 'Nilai Pengasuhan',
            'katrol1'          => 'Nilai Dinas',
            'katrol2'          => 'Ijazah',
            default            => '',
        };
    }
}

if (! function_exists('getLabelShort')) {
    /**
     * Mendapatkan label singkat berdasarkan jenis ujian.
     *
     * @param string|null $ujian
     * @return string
     */
    function getLabelShort(?string $ujian): string
    {
        return match ($ujian) {
            'nilai_harian'     => 'NH',
            'uts'              => 'UTS',
            'uas'              => 'UAS',
            'um'               => 'UM',
            'nilai_rapor'      => 'Raport',
            'nilai_pengasuhan' => 'Pengasuhan',
            'katrol1'          => 'Dinas',
            'katrol2'          => 'Ijazah',
            default            => '',
        };
    }
}
