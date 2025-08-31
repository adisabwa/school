<?php

use Modules\Presensi\Controllers\Admin\PresensiKelasController as PresensiKelas;

//-------------------------------------KMI Mapel -----------------------------------------------
$routes->group('presensi/admin', [
    'filter' => 'api-auth:mapel.admin',
], static function ($routes) {    

    //-------------------------------------Presensi Mengajar di Kelas -----------------------------------------------
    $routes->group('kelas', static function ($routes) {
        $routes->add('/', [PresensiKelas::class,'index']);
        $routes->add('get', [PresensiKelas::class, 'get']);
        $routes->add('delete/(:any)', [PresensiKelas::class,'delete/$1']);
        $routes->add('delete_many', [PresensiKelas::class,'delete_many']);
        $routes->add('download/(:any)', [PresensiKelas::class,'download/$1']);
        $routes->add('download_many', [PresensiKelas::class,'download_many']);
        $routes->add('options', [PresensiKelas::class,'options']);
    });
});

//-----------------------------------------------------------------------------------------------------

