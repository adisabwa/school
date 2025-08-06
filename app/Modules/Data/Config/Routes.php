<?php

use Modules\Data\Controllers\PenghasilanController;
use Modules\Data\Controllers\SantriController;
use Modules\Data\Controllers\PenggunaController;
use Modules\Data\Controllers\UnitController;
use Modules\Data\Controllers\JurusanController;
use Modules\Data\Controllers\GuruController;
use Modules\Data\Controllers\KelasController;
use Modules\Data\Controllers\SemesterController;

//----------------------------------Section Data-------------------------------------


//----------------------------------Data Guru-------------------------------------
$routes->group('data/guru', static function ($routes) {
    $routes->add('/', [GuruController::class, 'index']);
    $routes->add('get', [GuruController::class, 'get']);
    $routes->add('store', [GuruController::class, 'store'], [ 'filter' => 'api-validation:sch__guru']);
    $routes->add('delete/(:any)', [GuruController::class, 'delete/$1']);
    $routes->add('delete_many', [GuruController::class, 'delete_many']);
    $routes->add('template', [GuruController::class, 'template']);
    $routes->add('upload', [GuruController::class, 'upload']);
    $routes->add('options', [GuruController::class, 'options']);
    $routes->add('search', [GuruController::class, 'search']);
});

$routes->group('data', [
    'filter' => 'api-auth',
], static function ($routes) {
//----------------------------------Data Penghaslian-------------------------------------
    $routes->group('penghasilan', static function ($routes) {
        $routes->add('/', [PenghasilanController::class, 'index']);
        $routes->add('get', [PenghasilanController::class, 'get']);
        $routes->add('store', [PenghasilanController::class, 'store'], [ 'filter' => 'api-validation:sch__penghasilan']);
        $routes->add('delete/(:any)', [PenghasilanController::class, 'delete/$1']);
        $routes->add('delete_many', [PenghasilanController::class, 'delete_many']);
        $routes->add('options', [PenghasilanController::class, 'options']);
    });

    
    //----------------------------------Data Unit-------------------------------------
    $routes->group('unit', static function ($routes) {
        $routes->add('/', [UnitController::class, 'index']);
        $routes->add('get', [UnitController::class, 'get']);
        $routes->add('store', [UnitController::class, 'store'], [ 'filter' => 'api-validation:sch__unit']);
        $routes->add('delete/(:any)', [UnitController::class, 'delete/$1']);
        $routes->add('delete_many', [UnitController::class, 'delete_many']);
        $routes->add('options', [UnitController::class, 'options']);
    });
    
    //----------------------------------Data Jurusan-------------------------------------
    $routes->group('jurusan', static function ($routes) {
        $routes->add('/', [JurusanController::class, 'index']);
        $routes->add('get', [JurusanController::class, 'get']);
        $routes->add('store', [JurusanController::class, 'store'], [ 'filter' => 'api-validation:sch__jurusan']);
        $routes->add('delete/(:any)', [JurusanController::class, 'delete/$1']);
        $routes->add('delete_many', [JurusanController::class, 'delete_many']);
        $routes->add('template', [JurusanController::class, 'template']);
        $routes->add('upload', [JurusanController::class, 'upload']);
        $routes->add('options', [JurusanController::class, 'options']);
        $routes->add('search', [JurusanController::class, 'search']);
    });
    
    //----------------------------------Data Semester-------------------------------------
    $routes->group('semester', static function ($routes) {
        $routes->add('/', [SemesterController::class, 'index']);
        $routes->add('get', [SemesterController::class, 'get']);
        $routes->add('store', [SemesterController::class, 'store'], [ 'filter' => 'api-validation:sch__semester']);
        $routes->add('delete/(:any)', [SemesterController::class, 'delete/$1']);
        $routes->add('delete_many', [SemesterController::class, 'delete_many']);
        $routes->add('options', [SemesterController::class, 'options']);
    });

    //----------------------------------Data Kelas-------------------------------------
    $routes->group('kelas', static function ($routes) {
        $routes->add('/', [KelasController::class, 'index']);
        $routes->add('get', [KelasController::class, 'get']);
        $routes->add('store', [KelasController::class, 'store'], [ 'filter' => 'api-validation:sch__kelas']);
        $routes->add('delete/(:any)', [KelasController::class, 'delete/$1']);
        $routes->add('delete_many', [KelasController::class, 'delete_many']);
        $routes->add('template', [KelasController::class, 'template']);
        $routes->add('upload', [KelasController::class, 'upload']);
        $routes->add('options', [KelasController::class, 'options']);
        $routes->add('search', [KelasController::class, 'search']);
    });


    //----------------------------------Data Santri-------------------------------------
    $routes->group('santri', static function ($routes) {
        $routes->add('/', [SantriController::class, 'index']);
        $routes->add('get', [SantriController::class, 'get']);
        $routes->add('store', [SantriController::class, 'store'], [ 'filter' => 'api-validation:sch__santri']);
        $routes->add('delete/(:any)', [SantriController::class, 'delete/$1']);
        $routes->add('delete_many', [SantriController::class, 'delete_many']);
        $routes->add('template', [SantriController::class, 'template']);
        $routes->add('upload', [SantriController::class, 'upload']);
        $routes->add('options', [SantriController::class, 'options']);
        $routes->add('search', [SantriController::class, 'search']);
    });
    
    //----------------------------------Data Pengguna-------------------------------------
    $routes->group('pengguna', static function ($routes) {
        $routes->add('/', [PenggunaController::class, 'index']);
        $routes->add('get', [PenggunaController::class, 'get']);
        $routes->add('store', [PenggunaController::class, 'store'], [ 'filter' => 'api-validation:sch_pengguna']);
        $routes->add('delete/(:any)', [PenggunaController::class, 'delete/$1']);
        $routes->add('delete_many', [PenggunaController::class, 'delete_many']);
        $routes->add('template', [PenggunaController::class, 'template']);
        $routes->add('upload', [PenggunaController::class, 'upload']);
        $routes->add('options', [PenggunaController::class, 'options']);
        $routes->add('search', [PenggunaController::class, 'search']);
    });
});
    