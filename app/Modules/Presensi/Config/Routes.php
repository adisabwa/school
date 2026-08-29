<?php

use Modules\Presensi\Controllers\PresensiController as Presensi;
use Modules\Presensi\Controllers\Admin\PresensiMengajarController as PresensiMengajar;
use Modules\Presensi\Controllers\Admin\PresensiSantriController as PresensiSantri;

//-------------------------------------KMI Mapel -----------------------------------------------
$routes->group('presensi', [
    'filter' => 'api-auth:presensi.all',
], static function ($routes) {    

    $routes->add('create_notifications', [Presensi::class, 'createNotifications']);

helper('route');
    //-------------------------------------Presensi Mengajar di Kelas -----------------------------------------------
    $routes->group('mengajar', static function ($routes) {
        addDefaultRoutes($routes, PresensiMengajar::class, PREFIX_TABLE.'pre_mengajar_kelas');
        $routes->add('get_all', [PresensiMengajar::class, 'getAll']);
        $routes->add('get_all_grouping', [PresensiMengajar::class, 'getAllGrouping']);
        $routes->add('get_all_grouping_guru', [PresensiMengajar::class, 'getAllGroupingGuru']);
        $routes->add('summary', [PresensiMengajar::class, 'getSummary']);
    });
    //-------------------------------------Presensi Santri di Kelas -----------------------------------------------
    $routes->group('santri', static function ($routes) {
        addDefaultRoutes($routes, PresensiSantri::class, PREFIX_TABLE.'pre_harian');
        $routes->add('summary', [PresensiSantri::class, 'getSummary']);
    });
    //-------------------------------------Presensi Kedatangan Guru  -----------------------------------------------
    $routes->group('kedatangan', static function ($routes) {
        addDefaultRoutes($routes, Modules\Presensi\Controllers\PresensiKedatanganController::class, PREFIX_TABLE.'pre_kedatangan');
        $routes->add('summary', [Modules\Presensi\Controllers\PresensiKedatanganController::class, 'getSummary']);
    });
});

//-----------------------------------------------------------------------------------------------------

