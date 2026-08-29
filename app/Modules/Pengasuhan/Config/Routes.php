<?php


helper('route');
use Modules\Pengasuhan\Controllers\NilaiPengasuhanController;

$routes->group('pengasuhan', [
    'filter' => 'api-auth',
], static function ($routes) {

    //----------------------------------Data Nilai Pengasuhan -------------------------------------
    $routes->group('nilai', static function ($routes) {
        $routes->add('get_progres', [NilaiPengasuhanController::class,'get_progres']);
        $routes->add('store_many', [NilaiPengasuhanController::class,'store_many']);
        addDefaultRoutes($routes, NilaiPengasuhanController::class, PREFIX_TABLE.'peng_nilai');
    });

});