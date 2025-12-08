<?php

use Modules\Rapor\Controllers\RaporController;

helper('route');

//-------------------------------------Rapor Mapel -----------------------------------------------
$routes->group('rapor', [
    'filter' => 'api-auth:rapor.all',
], static function ($routes) {    

    //-------------------------------------Data Rapor KMI -----------------------------------------------
    $routes->group('/', static function ($routes) {
        $routes->add('rekapitulasi', [RaporController::class, 'rekapitulasi']);
        $routes->add('download_ledger', [RaporController::class, 'download_ledger']);
        $routes->add('download_raport', [RaporController::class, 'download_raport']);
        $routes->add('download_raport_pengasuhan', [RaporController::class, 'download_raport_pengasuhan']);
        $routes->add('get_nilai_rdm', [RaporController::class, 'get_nilai_rdm']);
        $routes->add('get_nilai_rdm_all', [RaporController::class, 'get_nilai_rdm_all']);
    });

});
//-----------------------------------------------------------------------------------------------------