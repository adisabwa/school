<?php

use Modules\Sholat\Controllers\Sholat;
use Modules\Sholat\Controllers\Wajib\SholatController as SholatWajib;
use Modules\Sholat\Controllers\Sunnah\SholatController as SholatSunnah;

//----------------------------------Section Tabungan-------------------------------------

// //----------------------------------Default Tabungan-------------------------------------
// $routes->group('sholat', static function ($routes) {
//     $routes->add('/', [Sholat::class,'get']);
//     $routes->add('get', [Sholat::class,'get']);
//     $routes->add('search', [Sholat::class,'search']);
//     $routes->add('store', [Sholat::class,'store'],[ 'filter' => 'api-validation:mu_sholat']);
//     $routes->add('template', [Sholat::class,'template']);
//     $routes->add('upload', [Sholat::class,'upload']);
//     $routes->add('preparation', [Sholat::class,'preparation']);
// });

//-------------------------------------Wajib Sholat -----------------------------------------------
$routes->group('sholat/wajib', [
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [SholatWajib::class,'index']);
    $routes->add('get', [SholatWajib::class, 'get']);
    $routes->add('get_where', [SholatWajib::class, 'get_where']);
    $routes->add('store', [SholatWajib::class, 'store'], [ 'filter' => 'api-validation:mu_sholat-wajib,sholat/wajib/total_score']);
    $routes->add('dashboard', [SholatWajib::class, 'dashboard']);
    $routes->add('get_last_and_best', [SholatWajib::class, 'get_last_and_best']);
    $routes->add('get_before', [SholatWajib::class, 'get_before']);
    $routes->add('delete/(:any)', [SholatWajib::class, 'delete/$1']);

});

//-----------------------------------------------------------------------------------------------------
//-------------------------------------Sunnah Sholat -----------------------------------------------
$routes->group('sholat/sunnah', [
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [SholatSunnah::class,'index']);
    $routes->add('get', [SholatSunnah::class, 'get']);
    $routes->add('get_initial', [SholatSunnah::class, 'get_initial']);
    $routes->add('get_where', [SholatSunnah::class, 'get_where']);
    $routes->add('store', [SholatSunnah::class, 'store'], [ 'filter' => 'api-validation:mu_sholat-sunnah,sholat/sunnah/total_score']);
    $routes->add('dashboard', [SholatSunnah::class, 'dashboard']);
    $routes->add('get_last_and_best', [SholatSunnah::class, 'get_last_and_best']);
    $routes->add('get_before', [SholatSunnah::class, 'get_before']);
    $routes->add('delete/(:any)', [SholatSunnah::class, 'delete/$1']);
    // $routes->add('summary', [SholatSunnah::class, 'summary']);

});

//-----------------------------------------------------------------------------------------------------

