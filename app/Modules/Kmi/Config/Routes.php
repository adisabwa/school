<?php

use Modules\Kmi\Controllers\KaldikController;
use Modules\Kmi\Controllers\Admin\KaldikController as Kaldik;

helper('route');
//----------------------------------Section KMI-------------------------------------

$routes->group('kmi/kaldik', static function ($routes) {    
    addDefaultRoutes($routes, KaldikController::class, PREFIX_TABLE.'aka_kaldik');
});

//------------------------------------- Kaldik -----------------------------------------------
$routes->group('kmi/admin/kaldik', [
    'filter' => 'api-auth:admin',
], static function ($routes) {    
    $routes->add('/', [Kaldik::class,'index']);
    $routes->add('get', [Kaldik::class, 'get']);
    $routes->add('store', [Kaldik::class, 'store'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'aka_kaldik' ]);
    $routes->add('template', [Kaldik::class, 'template']);
    $routes->add('upload', [Kaldik::class, 'upload']);
    $routes->add('dashboard', [Kaldik::class, 'dashboard']);
    $routes->add('delete/(:any)', [Kaldik::class,'delete/$1']);
    $routes->add('delete_many', [Kaldik::class,'delete_many']);
    $routes->add('download/(:any)', [Kaldik::class,'download/$1']);
    $routes->add('download_many', [Kaldik::class,'download_many']);
    $routes->add('download_kalender', [Kaldik::class,'download_kalender']);
});

//-----------------------------------------------------------------------------------------------------

