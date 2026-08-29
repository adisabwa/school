<?php

use Modules\Nilai\Controllers\MapelNilaiController as MapelNilai;

helper('route');

    //------------------------------------- Nilai Mapel -----------------------------------------------
$routes->group('nilai', [
    'filter' => 'api-auth:nilai.all',
], static function ($routes) {    

    $routes->add('/', [MapelNilai::class,'index']);
    $routes->add('get', [MapelNilai::class, 'get']);
    $routes->add('store', [MapelNilai::class, 'store']);
    $routes->add('store_many', [MapelNilai::class, 'store_many']);
    $routes->add('rekapitulasi', [MapelNilai::class, 'rekapitulasi']);
    $routes->add('download_ledger', [MapelNilai::class, 'download_ledger']);
    $routes->add('download_raport', [MapelNilai::class, 'download_raport']);
    $routes->add('download_raport_smk', [MapelNilai::class, 'download_raport_smk']);
    $routes->add('get_progres', [MapelNilai::class, 'get_progres']);
    $routes->add('download_template', [MapelNilai::class, 'download_template']);

});
