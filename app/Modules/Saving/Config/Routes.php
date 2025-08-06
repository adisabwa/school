<?php

use Modules\Saving\Controllers\Admin\SavingController as SavingAdmin;
use Modules\Saving\Controllers\Admin\RekapitulasiController as SavingRekapitulasi;

//----------------------------------Section Tabungan-------------------------------------

// //----------------------------------Default Tabungan-------------------------------------
// $routes->group('saving', static function ($routes) {
//     $routes->add('/', [Saving::class,'get']);
//     $routes->add('get', [Saving::class,'get']);
//     $routes->add('search', [Saving::class,'search']);
//     $routes->add('store', [Saving::class,'store'],[ 'filter' => 'api-validation:da_saving']);
//     $routes->add('template', [Saving::class,'template']);
//     $routes->add('upload', [Saving::class,'upload']);
//     $routes->add('preparation', [Saving::class,'preparation']);
// });

//-------------------------------------Admin Tabungan -----------------------------------------------
$routes->group('saving/admin', [
    'filter' => 'api-auth:saving.admin',
], static function ($routes) {    
    $routes->add('/', [SavingAdmin::class,'index']);
    $routes->add('get', [SavingAdmin::class, 'get']);
    $routes->add('store', [SavingAdmin::class, 'store'], [ 'filter' => 'api-validation:das_tabungan']);
    $routes->add('template', [SavingAdmin::class, 'template']);
    $routes->add('upload', [SavingAdmin::class, 'upload']);
    $routes->add('dashboard', [SavingAdmin::class, 'dashboard']);
    $routes->add('status/(:any)/(:any)', [SavingAdmin::class,'status/$1/$2']);
    $routes->add('status_many', [SavingAdmin::class,'status_many']);
    $routes->add('delete/(:any)', [SavingAdmin::class,'delete/$1']);
    $routes->add('delete_many', [SavingAdmin::class,'delete_many']);
    $routes->add('download/(:any)', [SavingAdmin::class,'download/$1']);
    $routes->add('download_many', [SavingAdmin::class,'download_many']);
    $routes->group('rekapitulasi', static function ($routes) {
        $routes->add('/', [SavingRekapitulasi::class,'index']);
        $routes->add('download', [SavingRekapitulasi::class,'download']);
    });
});

//-----------------------------------------------------------------------------------------------------

