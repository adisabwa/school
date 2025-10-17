<?php

use Modules\Mapel\Controllers\MapelNilaiController as MapelNilai;
use Modules\Mapel\Controllers\Admin\MapelController as MapelAdmin;
use Modules\Mapel\Controllers\Admin\MapelPembagianController as MapelPembagianAdmin;
use Modules\Mapel\Controllers\Admin\MapelPenjadwalanController as MapelPenjadwalanAdmin;
use Modules\Mapel\Controllers\Admin\MapelPenjadwalanDetailController as MapelPenjadwalanDetailAdmin;

helper('route');

//------------------------------------- All User Mapel -----------------------------------------------
$routes->group('mapel', [
    'filter' => 'api-auth:mapel.all',
], static function ($routes) {    

    //------------------------------------- Nilai Mapel -----------------------------------------------
    $routes->group('nilai', static function ($routes) {
        $routes->add('/', [MapelNilai::class,'index']);
        $routes->add('get', [MapelNilai::class, 'get']);
        $routes->add('store', [MapelNilai::class, 'store']);
        $routes->add('store_many', [MapelNilai::class, 'store_many']);
        $routes->add('rekapitulasi', [MapelNilai::class, 'rekapitulasi']);
        $routes->add('download_ledger', [MapelNilai::class, 'download_ledger']);
        $routes->add('download_raport', [MapelNilai::class, 'download_raport']);
        $routes->add('get_progress', [MapelNilai::class, 'get_progress']);
    });

    //------------------------------------- Data Pembagian Mapel -----------------------------------------------
    $routes->add('pembagian/get', [MapelPembagianAdmin::class, 'get']);

});


//-------------------------------------Admin Mapel -----------------------------------------------
$routes->group('mapel/admin', [
    'filter' => 'api-auth:mapel.admin',
], static function ($routes) {    

    //-------------------------------------Data Mapel -----------------------------------------------
    addDefaultRoutes($routes, MapelAdmin::class, 'sch_aka_mapel');
    
    //-------------------------------------Data Pembagian Mapel -----------------------------------------------
    $routes->group('pembagian', static function ($routes) {
        $routes->add('/', [MapelPembagianAdmin::class,'index']);
        $routes->add('get', [MapelPembagianAdmin::class, 'get']);
        $routes->add('store', [MapelPembagianAdmin::class, 'store'], [ 'filter' => 'api-validation:sch_aka_pembagian_mapel']);
        $routes->add('store_many', [MapelPembagianAdmin::class, 'store_many'], [ 'filter' => 'api-validation:sch_aka_pembagian_mapel,true']);
        $routes->add('dashboard', [MapelPembagianAdmin::class, 'dashboard']);
        $routes->add('delete/(:any)', [MapelPembagianAdmin::class,'delete/$1']);
        $routes->add('delete_many', [MapelPembagianAdmin::class,'delete_many']);
        $routes->add('download/(:any)', [MapelPembagianAdmin::class,'download/$1']);
        $routes->add('download_many', [MapelPembagianAdmin::class,'download_many']);
        $routes->add('options', [MapelPembagianAdmin::class,'options']);
        $routes->add('options_penjadwalan', [MapelPembagianAdmin::class,'options_penjadwalan']);

    });

    
    //------------------------------------- Penjadwalan Mapel -----------------------------------------------
    $routes->group('penjadwalan', static function ($routes) {
        $routes->add('/', [MapelPenjadwalanAdmin::class,'index']);
        $routes->add('get', [MapelPenjadwalanAdmin::class, 'get']);
        $routes->add('store', [MapelPenjadwalanAdmin::class, 'store'], [ 'filter' => 'api-validation:sch_aka_penjadwalan']);
        $routes->add('store_many', [MapelPenjadwalanAdmin::class, 'store_many']);
        $routes->add('delete/(:any)', [MapelPenjadwalanAdmin::class,'delete/$1']);
        $routes->add('delete_many', [MapelPenjadwalanAdmin::class,'delete_many']);
        $routes->add('options', [MapelPenjadwalanAdmin::class,'options']);
        
        //------------------------------------- Penjadwalan Detail Mapel -----------------------------------------------
        $routes->group('detail', static function ($routes) {
            $routes->add('/', [MapelPenjadwalanDetailAdmin::class,'index']);
            $routes->add('get', [MapelPenjadwalanDetailAdmin::class, 'get']);
            $routes->add('store', [MapelPenjadwalanDetailAdmin::class, 'store'], [ 'filter' => 'api-validation:sch_aka_penjadwalan_detail']);
            $routes->add('store_many', [MapelPenjadwalanDetailAdmin::class, 'store_many'], [ 'filter' => 'api-validation:sch_aka_penjadwalan_detail,true']);
            $routes->add('delete/(:any)', [MapelPenjadwalanDetailAdmin::class,'delete/$1']);
            $routes->add('delete_many', [MapelPenjadwalanDetailAdmin::class,'delete_many']);
            $routes->add('options', [MapelPenjadwalanDetailAdmin::class,'options']);
        });
    });
});

//-----------------------------------------------------------------------------------------------------