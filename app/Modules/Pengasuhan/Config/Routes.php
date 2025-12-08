<?php


helper('route');
use Modules\Pengasuhan\Controllers\NilaiPengasuhanController;

$routes->group('pengasuhan', [
    'filter' => 'api-auth',
], static function ($routes) {

    //----------------------------------Data Nilai Pengasuhan -------------------------------------
    $routes->group('nilai', static function ($routes) {
        $routes->add('store_many', [NilaiPengasuhanController::class,'store_many']);
        addDefaultRoutes($routes, NilaiPengasuhanController::class, 'sch_peng_nilai');
    });

});