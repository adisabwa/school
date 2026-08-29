<?php

use Modules\Rapor\Controllers\RaporController;

helper('route');

//-------------------------------------Rapor Mapel -----------------------------------------------
$routes->group('rapor', [
    'filter' => 'api-auth:rapor.all',
], static function ($routes) {    
    
    //-------------------------------------Data Rapor KMI -----------------------------------------------
    $routes->group('/', static function ($routes) {
        addDefaultRoutes($routes, RaporController::class, PREFIX_TABLE.'aka_rapor');
        
        $routes->add('rekapitulasi', [RaporController::class, 'rekapitulasi']);
        $routes->add('download_ledger', [RaporController::class, 'download_ledger']);
        $routes->add('download_ledger_akhir', [RaporController::class, 'download_ledger_akhir']);
        $routes->add('download_raport', [RaporController::class, 'download_raport']);
        $routes->add('download_raport_pengasuhan', [RaporController::class, 'download_raport_pengasuhan']);
        $routes->add('download_raport_smk', [RaporController::class, 'download_raport_smk']);
        $routes->add('get_nilai_rdm', [RaporController::class, 'get_nilai_rdm']);
        $routes->add('count_ranking', [RaporController::class, 'countRanking']);
        $routes->add('get_nilai_rdm_all', [RaporController::class, 'get_nilai_rdm_all']);
    });

});
//-----------------------------------------------------------------------------------------------------