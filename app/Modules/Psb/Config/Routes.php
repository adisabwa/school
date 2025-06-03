<?php

use Modules\Psb\Controllers\Psb;
use Modules\Psb\Controllers\Admin\Psb as PsbAdmin;

//----------------------------------Section PSB-------------------------------------

//----------------------------------Default PSB-------------------------------------
$routes->group('psb', static function ($routes) {
    $routes->add('/', [Psb::class,'get']);
    $routes->add('get', [Psb::class,'get']);
    $routes->add('search', [Psb::class,'search']);
    $routes->add('store', [Psb::class,'store'],[ 'filter' => 'api-validation:mu_psb']);
    $routes->add('template', [Psb::class,'template']);
    $routes->add('upload', [Psb::class,'upload']);
    $routes->add('preparation', [Psb::class,'preparation']);
});

//-------------------------------------Admin PSB -----------------------------------------------
$routes->group('psb/admin', [
    'filter' => 'api-auth:admin',
], static function ($routes) {    
    $routes->add('/', [PsbAdmin::class,'index']);
    $routes->add('dashboard', [PsbAdmin::class,'dashboard']);
    $routes->add('status/(:any)/(:any)', [PsbAdmin::class,'status/$1/$2']);
    $routes->add('status_many', [PsbAdmin::class,'status_many']);
    $routes->add('delete/(:any)', [PsbAdmin::class,'delete/$1']);
    $routes->add('delete_many', [PsbAdmin::class,'delete_many']);
    $routes->add('download/(:any)', [PsbAdmin::class,'download/$1']);
    $routes->add('download_many', [PsbAdmin::class,'download_many']);
});

//-----------------------------------------------------------------------------------------------------

