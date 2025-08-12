<?php

use Modules\Mapel\Controllers\MapelNilaiController as MapelNilai;

use Modules\Mapel\Controllers\Admin\MapelController as MapelAdmin;
use Modules\Mapel\Controllers\Admin\MapelPembagianController as MapelPembagianAdmin;

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
    });


});


//-------------------------------------Admin Mapel -----------------------------------------------
$routes->group('mapel/admin', [
    'filter' => 'api-auth:mapel.admin',
], static function ($routes) {    

    //-------------------------------------Data Mapel -----------------------------------------------
        $routes->add('/', [MapelAdmin::class,'index']);
        $routes->add('get', [MapelAdmin::class, 'get']);
        $routes->add('store', [MapelAdmin::class, 'store'], [ 'filter' => 'api-validation:sch_aka_mapel']);
        $routes->add('store_many', [MapelAdmin::class, 'store_many'], [ 'filter' => 'api-validation:sch_aka_mapel,true']);
        $routes->add('dashboard', [MapelAdmin::class, 'dashboard']);
        $routes->add('delete/(:any)', [MapelAdmin::class,'delete/$1']);
        $routes->add('delete_many', [MapelAdmin::class,'delete_many']);
        $routes->add('download/(:any)', [MapelAdmin::class,'download/$1']);
        $routes->add('download_many', [MapelAdmin::class,'download_many']);
        $routes->add('options', [MapelAdmin::class,'options']);
    
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
    });
});

//-----------------------------------------------------------------------------------------------------

