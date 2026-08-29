<?php
use Modules\Ekstra\Controllers\Tsdac\NilaiController;
helper('route');

//----------------------------------Data Daerah -------------------------------------
$routes->group('ekstra', static function ($routes) {
    $routes->group('ts', static function ($routes) {
        $routes->group('tsdac', static function ($routes) {
            $routes->group('penilaian', static function ($routes) {
                addDefaultRoutes($routes, NilaiController::class, PREFIX_TABLE.'ts_tsdac_penilaian');
                $routes->add('get_match_results', [NilaiController::class, 'get_match_results']);
                $routes->add('get_current_match', [NilaiController::class, 'get_current_match']);
                $routes->add('set_current_match', [NilaiController::class, 'set_current_match']);
                $routes->add('summary', [NilaiController::class, 'summary']);
            });
            $routes->group('peserta', static function ($routes) {
                addDefaultRoutes($routes, Modules\Ekstra\Controllers\Tsdac\PesertaController::class, PREFIX_TABLE.'ts_tsdac_peserta');
            });
        });
    });
});