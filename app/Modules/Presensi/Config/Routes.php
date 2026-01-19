<?php

use Modules\Presensi\Controllers\Admin\PresensiMengajarController as PresensiMengajar;
use Modules\Presensi\Controllers\Admin\PresensiSantriController as PresensiSantri;

//-------------------------------------KMI Mapel -----------------------------------------------
$routes->group('presensi', [
    'filter' => 'api-auth:presensi.all',
], static function ($routes) {    

helper('route');
    //-------------------------------------Presensi Mengajar di Kelas -----------------------------------------------
    $routes->group('mengajar', static function ($routes) {
        addDefaultRoutes($routes, PresensiMengajar::class, 'sch_pre_mengajar_kelas');
        $routes->add('get_all', [PresensiMengajar::class, 'getAll']);
        $routes->add('get_all_grouping', [PresensiMengajar::class, 'getAllGrouping']);
        $routes->add('summary', [PresensiMengajar::class, 'getSummary']);
    });
    //-------------------------------------Presensi Santri di Kelas -----------------------------------------------
    $routes->group('santri', static function ($routes) {
        addDefaultRoutes($routes, PresensiSantri::class, 'sch_pre_harian');
        $routes->add('summary', [PresensiSantri::class, 'getSummary']);
    });
});

//-----------------------------------------------------------------------------------------------------

