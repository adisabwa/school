<?php

use Modules\Data\Controllers\PenghasilanController;
use Modules\Data\Controllers\SantriController;
use Modules\Data\Controllers\PenggunaController;
use Modules\Data\Controllers\UnitController;
use Modules\Data\Controllers\JurusanController;
use Modules\Data\Controllers\GuruController;
use Modules\Data\Controllers\KelasController;
use Modules\Data\Controllers\KelasAjarController;
use Modules\Data\Controllers\KamarController;
use Modules\Data\Controllers\SemesterController;
use Modules\Data\Controllers\SesiController;

//----------------------------------Section Data-------------------------------------

helper('route');
//----------------------------------Data Guru-------------------------------------
$routes->group('data/guru', static function ($routes) {
    addDefaultRoutes($routes, GuruController::class, PREFIX_TABLE.'_guru');
});

$routes->group('data', [
    'filter' => 'api-auth',
], static function ($routes) {
//----------------------------------Data Penghaslian-------------------------------------
    $routes->group('penghasilan', static function ($routes) {
        addDefaultRoutes($routes, PenghasilanController::class, PREFIX_TABLE.'_penghasilan');
    });

    
    //----------------------------------Data Unit-------------------------------------
    $routes->group('unit', static function ($routes) {
        addDefaultRoutes($routes, UnitController::class, PREFIX_TABLE.'_unit');
    });

    
    //----------------------------------Data Sesi-------------------------------------
    $routes->group('sesi', static function ($routes) {
        addDefaultRoutes($routes, SesiController::class, PREFIX_TABLE.'_sesi');
        $routes->add('sesi_now', [SesiController::class, 'sesi_now']);
    });
    
    //----------------------------------Data Jurusan-------------------------------------
    $routes->group('jurusan', static function ($routes) {
        $routes->add('/', [JurusanController::class, 'index']);
        $routes->add('get', [JurusanController::class, 'get']);
        $routes->add('store', [JurusanController::class, 'store'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'_jurusan']);
        $routes->add('delete/(:any)', [JurusanController::class, 'delete/$1']);
        $routes->add('delete_many', [JurusanController::class, 'delete_many']);
        $routes->add('template', [JurusanController::class, 'template']);
        $routes->add('upload', [JurusanController::class, 'upload']);
        $routes->add('options', [JurusanController::class, 'options']);
        $routes->add('search', [JurusanController::class, 'search']);
    });
    
    //----------------------------------Data Semester-------------------------------------
    $routes->group('semester', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\SemesterController::class, PREFIX_TABLE.'_semester');
        $routes->add('semester_now', [SemesterController::class, 'semester_now']);
        $routes->add('options_tahun_ajaran', [SemesterController::class, 'options_tahun_ajaran']);
    });

    //----------------------------------Data Kamar-------------------------------------
    $routes->group('kamar', static function ($routes) {
        addDefaultRoutes($routes, KamarController::class, PREFIX_TABLE.'_kamar');
    });

    
    //----------------------------------Data Kelas-------------------------------------
    $routes->group('kelas', static function ($routes) {
        addDefaultRoutes($routes, KelasController::class, PREFIX_TABLE.'_kelas');
    });

    //----------------------------------Data Kelas Per Tahun -------------------------------------
    $routes->group('kelas-ajar', static function ($routes) {
        addDefaultRoutes($routes, KelasAjarController::class, PREFIX_TABLE.'_kelas_ajar');
    });

    //----------------------------------Data Santri-------------------------------------
    $routes->group('santri', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\SantriController::class, PREFIX_TABLE.'_santri');
        $routes->add('options_kelas', [SantriController::class, 'options_kelas']);
    });
    
    //----------------------------------Data Santri Kamar -------------------------------------
    $routes->group('santri-kamar', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\SantriKamarController::class, PREFIX_TABLE.'_santri_kamar');
    });
    
    //----------------------------------Data Santri Kelas -------------------------------------
    $routes->group('santri-kelas', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\SantriKelasController::class, PREFIX_TABLE.'_santri_kelas');
        $routes->add('total-santri', [Modules\Data\Controllers\SantriKelasController::class, 'getTotalSantri']);
    });

    //----------------------------------Data Daerah -------------------------------------
    $routes->group('daerah', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\DaerahController::class, PREFIX_TABLE.'_daerah');
    });

    //----------------------------------Data Jabatan -------------------------------------
    $routes->group('jabatan', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\JabatanController::class, PREFIX_TABLE.'_jabatan');
    });

    //----------------------------------Data Jabatan Guru -------------------------------------
    $routes->group('jabatan-guru', static function ($routes) {
        addDefaultRoutes($routes, Modules\Data\Controllers\JabatanGuruController::class, PREFIX_TABLE.'_jabatan_guru');
    });


    //----------------------------------Data Pengguna-------------------------------------
    $routes->group('pengguna', static function ($routes) {
        $routes->add('/', [PenggunaController::class, 'index']);
        $routes->add('get', [PenggunaController::class, 'get']);
        $routes->add('store', [PenggunaController::class, 'store'], [ 'filter' => 'api-validation:'.PREFIX_TABLE.'_guru']);
        $routes->add('delete/(:any)', [PenggunaController::class, 'delete/$1']);
        $routes->add('delete_many', [PenggunaController::class, 'delete_many']);
        $routes->add('template', [PenggunaController::class, 'template']);
        $routes->add('upload', [PenggunaController::class, 'upload']);
        $routes->add('options', [PenggunaController::class, 'options']);
        $routes->add('search', [PenggunaController::class, 'search']);
    });
});
    