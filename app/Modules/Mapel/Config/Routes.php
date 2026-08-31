<?php

use Modules\Mapel\Controllers\MapelNilaiController as MapelNilai;
use Modules\Mapel\Controllers\Admin\MapelController as MapelAdmin;
use Modules\Mapel\Controllers\Admin\MapelPembagianController as MapelPembagianAdmin;
use Modules\Mapel\Controllers\Admin\MapelPenjadwalanController as MapelPenjadwalanAdmin;
use Modules\Mapel\Controllers\MapelMateriController as MapelMateriController;
use Modules\Mapel\Controllers\MapelSubMateriController as MapelSubMateriController;

helper('route');

//------------------------------------- All User Mapel -----------------------------------------------
$routes->group('mapel', [
    'filter' => 'api-auth:mapel.all',
], static function ($routes) {    

    //------------------------------------- Data Pembagian Mapel -----------------------------------------------
    $routes->add('pembagian/get', [MapelPembagianAdmin::class, 'get']);
    $routes->add('pembagian/store', [MapelPembagianAdmin::class, 'store'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'aka_pembagian_mapel']);

    
    //------------------------------------- Data Penjadwalan Mapel -----------------------------------------------
    $routes->add('penjadwalan', [MapelPenjadwalanAdmin::class, 'index']);

    
    //-------------------------------------Rpp -----------------------------------------------
    $routes->group('materi', static function ($routes) {
        addDefaultRoutes($routes, MapelMateriController::class, PREFIX_TABLE.'aka_materi');
        $routes->add('summary', [MapelMateriController::class,'getSummary']);
        $routes->add('generate-list-materi', [MapelMateriController::class,'generateListMateri']);
        $routes->add('generate-materi', [MapelMateriController::class,'generateMateri']);
        $routes->add('saran-asesmen', [MapelMateriController::class,'saranAsesmen']);
        $routes->add('generate-rpp', [MapelMateriController::class,'generateRpp']);
    });
    //-------------------------------------Rpp -----------------------------------------------
    $routes->group('sub-materi', static function ($routes) {
        addDefaultRoutes($routes, MapelSubMateriController::class, PREFIX_TABLE.'aka_sub_materi');
    });
});


//-------------------------------------Admin Mapel -----------------------------------------------
$routes->group('mapel/admin', [
    'filter' => 'api-auth:mapel.admin',
], static function ($routes) {    

    //-------------------------------------Data Mapel -----------------------------------------------
    addDefaultRoutes($routes, MapelAdmin::class, PREFIX_TABLE.'aka_mapel');
    
    //-------------------------------------Data Pembagian Mapel -----------------------------------------------
    $routes->group('pembagian', static function ($routes) {
        $routes->add('/', [MapelPembagianAdmin::class,'index']);
        $routes->add('get', [MapelPembagianAdmin::class, 'get']);
        $routes->add('get_where', [MapelPembagianAdmin::class, 'get_where']);
        $routes->add('store', [MapelPembagianAdmin::class, 'store'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'aka_pembagian_mapel']);
        $routes->add('store_many', [MapelPembagianAdmin::class, 'store_many'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'aka_pembagian_mapel',true]);
        $routes->add('dashboard', [MapelPembagianAdmin::class, 'dashboard']);
        $routes->add('delete/(:any)', [MapelPembagianAdmin::class,'delete/$1']);
        $routes->add('delete_many', [MapelPembagianAdmin::class,'delete_many']);
        $routes->add('download/(:any)', [MapelPembagianAdmin::class,'download/$1']);
        $routes->add('download_many', [MapelPembagianAdmin::class,'download_many']);
        $routes->add('options', [MapelPembagianAdmin::class,'options']);
        $routes->add('options_penjadwalan', [MapelPembagianAdmin::class,'options_penjadwalan']);
        $routes->add('upload', [MapelPembagianAdmin::class,'upload']);

    });

    
    //------------------------------------- Penjadwalan Mapel -----------------------------------------------
    $routes->group('penjadwalan', static function ($routes) {
        $routes->add('/', [MapelPenjadwalanAdmin::class,'index']);
        $routes->add('get', [MapelPenjadwalanAdmin::class, 'get']);
        $routes->add('store', [MapelPenjadwalanAdmin::class, 'store'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'aka_penjadwalan']);
        $routes->add('store_many', [MapelPenjadwalanAdmin::class, 'store_many']);
        $routes->add('delete/(:any)', [MapelPenjadwalanAdmin::class,'delete/$1']);
        $routes->add('delete_many', [MapelPenjadwalanAdmin::class,'delete_many']);
        $routes->add('options', [MapelPenjadwalanAdmin::class,'options']);
        $routes->add('upload', [MapelPenjadwalanAdmin::class,'upload']);
    });

});

//-----------------------------------------------------------------------------------------------------