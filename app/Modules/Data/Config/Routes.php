<?php

use Modules\Data\Controllers\PenghasilanController;
use Modules\Data\Controllers\SantriController;

//----------------------------------Section Data-------------------------------------
$routes->group('data', [
    'filter' => 'api-auth:admin',
], static function ($routes) {
//----------------------------------Data Penghaslian-------------------------------------
    $routes->group('penghasilan', static function ($routes) {
        $routes->add('/', [PenghasilanController::class, 'index']);
        $routes->add('prodi', [PenghasilanController::class, 'prodi']);
        $routes->add('get', [PenghasilanController::class, 'get']);
        $routes->add('store', [PenghasilanController::class, 'store'], [ 'filter' => 'api-validation:sch__penghasilan']);
        $routes->add('delete/(:any)', [PenghasilanController::class, 'delete/$1']);
        $routes->add('delete_many', [PenghasilanController::class, 'delete_many']);
        $routes->add('template', [PenghasilanController::class, 'template']);
        $routes->add('options', [PenghasilanController::class, 'options']);
        $routes->add('upload', [PenghasilanController::class, 'upload']);
    });

    
    //----------------------------------Data Santri-------------------------------------
    $routes->group('santri', static function ($routes) {
        $routes->add('/', [SantriController::class, 'index']);
        $routes->add('prodi', [SantriController::class, 'prodi']);
        $routes->add('get', [SantriController::class, 'get']);
        $routes->add('store', [SantriController::class, 'store'], [ 'filter' => 'api-validation:sch__santri']);
        $routes->add('delete/(:any)', [SantriController::class, 'delete/$1']);
        $routes->add('delete_many', [SantriController::class, 'delete_many']);
        $routes->add('template', [SantriController::class, 'template']);
        $routes->add('upload', [SantriController::class, 'upload']);
        $routes->add('kelas', [SantriController::class, 'kelas']);
        $routes->add('options', [SantriController::class, 'options']);
        $routes->add('search', [SantriController::class, 'search']);
    });
});
    