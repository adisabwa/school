<?php

use Modules\Data\Controllers\GroupController as Group;
use Modules\Data\Controllers\GroupActivityController as GroupActivity;
use Modules\Data\Controllers\UnitController as Unit;
use Modules\Data\Controllers\AnggotaController as Anggota;
use Modules\Data\Controllers\SholatSunnahController as SholatSunnah;
use Modules\Data\Controllers\Bacaan\SholatController as BacaanSholat;
use Modules\Data\Controllers\Bacaan\DoaController as BacaanDoa;
use Modules\Data\Controllers\Bacaan\DzikirSholatController as BacaanDzikirSholat;

//----------------------------------Section Data-------------------------------------

//----------------------------------Data Sholat Sunnah-------------------------------------
$routes->group('data/sholat-sunnah', static function ($routes) {
    $routes->add('/', [SholatSunnah::class, 'index']);
    $routes->add('get', [SholatSunnah::class, 'get']);
    $routes->add('reset_options', [SholatSunnah::class, 'resetOptions']);
    $routes->add('store', [SholatSunnah::class, 'store'], [ 'filter' => 'api-validation:mu__sholat_sunnah']);
    $routes->add('delete/(:any)', [SholatSunnah::class, 'delete/$1']);
    $routes->add('delete_many', [SholatSunnah::class, 'delete_many']);
    $routes->add('template', [SholatSunnah::class, 'template']);
    $routes->add('upload', [SholatSunnah::class, 'upload']);
    $routes->add('options', [SholatSunnah::class, 'options']);
});


//----------------------------------Data Anggota-------------------------------------
$routes->group('data/anggota', static function ($routes) {
    $routes->add('get', [Anggota::class, 'get']);
    $routes->add('get_where', [Anggota::class, 'get_where']);
    $routes->add('store', [Anggota::class, 'store'], [ 'filter' => 'api-validation:mu_anggota']);
});


//----------------------------------Data Unit-------------------------------------
$routes->group('data/unit', static function ($routes) {
    $routes->add('/', [Unit::class, 'index']);
    $routes->add('/', [Unit::class, 'index']);
    $routes->add('get', [Unit::class, 'get']);
    $routes->add('store', [Unit::class, 'store'], [ 'filter' => 'api-validation:mu__unit_kerja']);
    $routes->add('delete/(:any)', [Unit::class, 'delete/$1']);
    $routes->add('delete_many', [Unit::class, 'delete_many']);
    $routes->add('options', [Unit::class, 'options']);
});
        
$routes->group('data', [
    'filter' => 'api-auth',
], static function ($routes) {
//----------------------------------Data Group-------------------------------------
    $routes->group('group', static function ($routes) {
        $routes->add('/', [Group::class, 'index']);
        $routes->add('get', [Group::class, 'get']);
        $routes->add('store', [Group::class, 'store'], [ 'filter' => 'api-validation:mu_group']);
        $routes->add('get_anggota', [Group::class, 'get_anggota']);
        $routes->add('delete/(:any)', [Group::class, 'delete/$1']);
        $routes->add('delete_many', [Group::class, 'delete_many']);
        $routes->add('template', [Group::class, 'template']);
        $routes->add('options', [Group::class, 'options']);
        $routes->add('upload', [Group::class, 'upload']);
    });
    
//----------------------------------Data Anggota-------------------------------------
    $routes->group('anggota', static function ($routes) {
        $routes->add('/', [Anggota::class, 'index']);
        $routes->add('prodi', [Anggota::class, 'prodi']);
        $routes->add('delete/(:any)', [Anggota::class, 'delete/$1']);
        $routes->add('delete_many', [Anggota::class, 'delete_many']);
        $routes->add('template', [Anggota::class, 'template']);
        $routes->add('upload', [Anggota::class, 'upload']);
        $routes->add('kelas', [Anggota::class, 'kelas']);
        $routes->add('options', [Anggota::class, 'options']);
        $routes->add('search', [Anggota::class, 'search']);
    });

    
//----------------------------------Data Bacaan-------------------------------------
$routes->group('bacaan', static function ($routes) {

//----------------------------------Bacaan Sholat-------------------------------------
    $routes->group('sholat', static function ($routes) {
        $routes->add('/', [BacaanSholat::class, 'index']);
        $routes->add('options', [BacaanSholat::class, 'options']);
        $routes->add('search', [BacaanSholat::class, 'search']);
    });

//----------------------------------Bacaan Dzikir Sholat-------------------------------------
    $routes->group('dzikir-sholat', static function ($routes) {
        $routes->add('/', [BacaanDzikirSholat::class, 'index']);
        $routes->add('options', [BacaanDzikirSholat::class, 'options']);
        $routes->add('search', [BacaanDzikirSholat::class, 'search']);
    });

    
//----------------------------------Bacaan Doa-------------------------------------
$routes->group('doa', static function ($routes) {
    $routes->add('/', [BacaanDoa::class, 'index']);
    $routes->add('options', [BacaanDoa::class, 'options']);
    $routes->add('search', [BacaanDoa::class, 'search']);
});
});

//----------------------------------Data Group Activity-------------------------------------
$routes->group('group/activity', static function ($routes) {
    $routes->add('/', [GroupActivity::class, 'index']);
    $routes->add('prodi', [GroupActivity::class, 'prodi']);
    $routes->add('get', [GroupActivity::class, 'get']);
    $routes->add('reset_options', [GroupActivity::class, 'resetOptions']);
    $routes->add('store', [GroupActivity::class, 'store'], [ 'filter' => 'api-validation:mu_group_activity']);
    $routes->add('delete/(:any)', [GroupActivity::class, 'delete/$1']);
    $routes->add('delete_many', [GroupActivity::class, 'delete_many']);
    $routes->add('options', [GroupActivity::class, 'options']);
});



//----------------------------------Data Unit-------------------------------------
    $routes->group('iqab', static function ($routes) {
        $routes->add('/', [Unit::class, 'index']);
        $routes->add('get', [Unit::class, 'get']);
        $routes->add('reset_options', [Unit::class, 'resetOptions']);
        $routes->add('store', [Unit::class, 'store'], [ 'filter' => 'api-validation:mu__unit_kerja']);
        $routes->add('delete/(:any)', [Unit::class, 'delete/$1']);
        $routes->add('delete_many', [Unit::class, 'delete_many']);
        $routes->add('template', [Unit::class, 'template']);
        $routes->add('upload', [Unit::class, 'upload']);
        $routes->add('options', [Unit::class, 'options']);
    });

//----------------------------------Data Prm Iqab-------------------------------------
    $routes->group('pelanggaran_iqab', static function ($routes) {
        $routes->add('/', [PrmIqab::class, 'index']);
        $routes->add('prodi', [PrmIqab::class, 'prodi']);
        $routes->add('get', [PrmIqab::class, 'get']);
        $routes->add('reset_options', [PrmIqab::class, 'resetOptions']);
        $routes->add('store', [PrmIqab::class, 'store'], [ 'filter' => 'api-validation:daiq_pelanggaran_iqab']);
        $routes->add('delete/(:any)', [PrmIqab::class, 'delete/$1']);
        $routes->add('delete_many', [PrmIqab::class, 'delete_many']);
        $routes->add('template', [PrmIqab::class, 'template']);
        $routes->add('upload', [PrmIqab::class, 'upload']);
        $routes->add('options', [PrmIqab::class, 'options']);
    });
});